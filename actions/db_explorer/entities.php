<?php

$dbprefix = elgg_get_config('dbprefix');

$page = get_input('page', 1);

$limit = get_input('rows', 50);

$sidx = get_input('sidx', 'e.guid');
$sord = get_input('sord', 'asc');

$type = get_input('type', 'user');
// Whitelist valid entity types for SQL safety
if (!in_array($type, ['user', 'object', 'group', 'site'])) {
	$type = 'user';
}

$filters = json_decode(get_input('filters', '[]'), true);
if (!is_array($filters)) {
	$filters = [];
}

$searchField = get_input('searchField');
$searchString = get_input('searchString');
$searchOper = get_input('searchOper');
if ($searchField && $searchString && $searchOper) {
	$filters['rules'][] = [
		'field' => $searchField,
		'op' => $searchOper,
		'data' => $searchString,
	];
}

$search_query = '';

if (is_array($filters['rules'])) {
	$search_queries = [];
	$groupOp = elgg_extract('groupOp', $filters, 'AND');

	foreach ($filters['rules'] as $rule) {
		// Validate field name against whitelist of known entity columns
		$valid_fields = ['e.guid', 'e.type', 'e.subtype', 'e.owner_guid', 'e.container_guid', 'e.access_id', 'e.time_created', 'e.time_updated', 'e.last_action', 'e.enabled'];
		$searchField = $rule['field'];
		if (!in_array($searchField, $valid_fields)) {
			continue;
		}

		$searchString = addslashes($rule['data']);

		list($searchFieldTable, $searchFieldName) = explode('.', $searchField);

		$searchOper = $rule['op'];

		if (!$searchString || !$searchField || !$searchOper) {
			continue;
		}

		switch ($searchOper) {
			case 'eq':
				$search_queries[] = "$searchField = '$searchString'";
				break;

			case 'ne':
				$search_queries[] = "$searchField != '$searchString'";
				break;

			case 'bw':
				$search_queries[] = "$searchField LIKE '$searchString%'";
				break;

			case 'bn':
				$search_queries[] = "$searchField NOT LIKE '$searchString%'";
				break;

			case 'ew':
				$search_queries[] = "$searchField LIKE '%$searchString'";
				break;

			case 'en':
				$search_queries[] = "$searchField NOT LIKE '%$searchString'";
				break;

			case 'cn':
				$search_queries[] = "$searchField LIKE '%$searchString%'";
				break;

			case 'nc':
				$search_queries[] = "$searchField NOT LIKE '%$searchString%'";
				break;

			case 'in':
				$in = explode(',', $searchString);
				$in_str_parts = [];
				foreach ($in as $in_l) {
					$in_str_parts[] = "'" . trim($in_l) . "'";
				}

				$in_str = implode(',', $in_str_parts);
				$search_queries[] = "$searchField IN ($in_str)";
				break;

			case 'ni':
				$in = explode(',', $searchString);
				$in_str_parts = [];
				foreach ($in as $in_l) {
					$in_str_parts[] = "'" . trim($in_l) . "'";
				}

				$in_str = implode(',', $in_str_parts);
				$search_queries[] = "$searchField NOT IN ($in_str)";
				break;
		}
	}

	if (count($search_queries)) {
		$search_query = ' AND ' . implode(" $groupOp ", $search_queries);
	}
}

// In Elgg 3.x, entity subtables (users_entity, groups_entity, etc.) have been removed.
// All entity data is now in the entities table only.
$join_query = '';

$guid = get_input('guid', false);

if ($guid) {
	$guid = (int) $guid;
	$count = 1;
} else {
	$row_count = get_data("SELECT COUNT(*) AS count FROM {$dbprefix}entities e WHERE e.type = '$type' $search_query");
	$count = $row_count[0]->count;
}

if ($count > 0) {
	$total_pages = ceil($count / $limit);
} else {
	$total_pages = 0;
}

if ($page > $total_pages) {
	$page = $total_pages;
}

$offset = $limit * $page - $limit;

$offset = ($offset < 0) ? 0 : $offset;

$query = "SELECT * FROM {$dbprefix}entities e";

if ($guid) {
	$query .= " WHERE e.guid = $guid";
} else {
	$query .= " WHERE e.type = '$type'";
}

$query .= $search_query;

$query .= " ORDER BY $sidx $sord LIMIT $limit OFFSET $offset";

$row_data = get_data($query);

if (!empty($row_data)) {
	$i = 0;

	// In Elgg 3.x, all entity types share the same columns in the entities table.
	// Type-specific columns (username, email, title, etc.) are now stored as metadata.
	$ordered_cols = [
		'checkbox',
		'guid',
		'type',
		'subtype',
		'owner_guid',
		'container_guid',
		'access_id',
		'time_created',
		'time_updated',
		'last_action',
		'enabled',
	];

	foreach ($row_data as $r) {
		$results['rows'][$i]['id'] = $r->guid;
		$r_vars = get_object_vars($r);

		foreach ($ordered_cols as $col) {
			$value = $r_vars[$col];

			if (elgg_view_exists("framework/db_explorer/db_column/$col")) {
				$results['rows'][$i]['cell'][] = elgg_view("framework/db_explorer/db_column/$col", [
					'data' => $r,
				]);
			} else {
				$results['rows'][$i]['cell'][] = $value;
			}
		}

		$i++;
	}
}

$results['page'] = $page;
$results['total'] = $total_pages;
$results['records'] = $count;

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-type: application/json; charset=UTF-8');

print json_encode($results);
exit;
