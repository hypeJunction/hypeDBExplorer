<?php

$dbprefix = elgg_get_config('dbprefix');

$page = get_input('page', 1);

$limit = get_input('rows', 50);

$sidx = get_input('sidx', 'e.guid');
$sord = get_input('sord', 'asc');

$searchField = get_input('searchField');
$searchString = get_input('searchString');
$searchOper = get_input('searchOper');

if ($searchField && $searchString && $searchOper) {

	$searchString = sanitise_string($searchString);
	$searchField = sanitise_string($searchField);

	switch ($searchOper) {

		case 'eq' :
			$search_query = " AND $searchField = '$searchString'";
			break;

		case 'ne' :
			$search_query = " AND $searchField != '$searchString'";
			break;

		case 'bw' :
			$search_query = " AND $searchField LIKE '$searchString%'";
			break;

		case 'bn' :
			$search_query = " AND $searchField NOT LIKE '$searchString%'";
			break;

		case 'ew' :
			$search_query = " AND $searchField LIKE '%$searchString'";
			break;

		case 'en' :
			$search_query = " AND $searchField NOT LIKE '%$searchString'";
			break;

		case 'cn' :
			$search_query = " AND $searchField LIKE '%$searchString%'";
			break;

		case 'nc' :
			$search_query = " AND $searchField NOT LIKE '%$searchString%'";
			break;

		case 'in' :
			$in = explode(',', $searchString);
			foreach ($in as $in_l) {
				$in_str[] = "'" . trim($in_l) . "'";
			}
			$in_str = implode(',', $in_str);
			$search_query = " AND $searchField IN ($in_str)";
			break;

		case 'ni' :
			$in = explode(',', $searchString);
			foreach ($in as $in_l) {
				$in_str[] = "'" . trim($in_l) . "'";
			}
			$in_str = implode(',', $in_str);
			$search_query = " AND $searchField NOT IN ($in_str)";
			break;
	}
}

$type = sanitize_string(get_input('type', 'user'));

switch ($type) {
	case 'user' :
		$join_query = " JOIN {$dbprefix}users_entity ue ON e.guid = ue.guid";
		break;

	case 'group' :
		$join_query = " JOIN {$dbprefix}groups_entity ge ON e.guid = ge.guid";
		break;

	case 'object' :
		$join_query = " JOIN {$dbprefix}objects_entity oe ON e.guid = oe.guid";
		break;

	case 'site' :
		$join_query = " JOIN {$dbprefix}sites_entity se ON e.guid = se.guid";
		break;
}

$guid = get_input('guid', false);

if ($guid) {
	$guid = sanitize_int($guid);
	$count = 1;
} else {
	$row_count = get_data("SELECT COUNT(*) AS count FROM {$dbprefix}entities e $join_query WHERE e.type = '$type' $search_query");
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

$query .= $join_query;

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

	switch ($type) {

		case 'user' :
			$ordered_cols = array(
				'guid',
				'username',
				'name',
				'email',
				'admin',
				'banned',
				'type',
				'subtype',
				'owner_guid',
				'site_guid',
				'container_guid',
				'access_id',
				'time_created',
				'time_updated',
				'last_action',
				'enabled'
			);
			break;

		case 'group' :
			$ordered_cols = array(
				'guid',
				'name',
				'description',
				'type',
				'subtype',
				'owner_guid',
				'site_guid',
				'container_guid',
				'access_id',
				'time_created',
				'time_updated',
				'last_action',
				'enabled'
			);
			break;

		case 'object' :
			$ordered_cols = array(
				'guid',
				'title',
				'description',
				'type',
				'subtype',
				'owner_guid',
				'site_guid',
				'container_guid',
				'access_id',
				'time_created',
				'time_updated',
				'last_action',
				'enabled'
			);
			break;

		case 'site' :
			$ordered_cols = array(
				'guid',
				'name',
				'description',
				'url',
				'type',
				'subtype',
				'owner_guid',
				'site_guid',
				'container_guid',
				'access_id',
				'time_created',
				'time_updated',
				'last_action',
				'enabled'
			);
			break;
	}
	foreach ($row_data as $r) {

		$results['rows'][$i]['id'] = $r->guid;
		$r_vars = get_object_vars($r);

		foreach ($ordered_cols as $col) {
			$value = $r_vars[$col];

			if (elgg_view_exists("framework/db_explorer/db_column/$col")) {
				$results['rows'][$i]['cell'][] = elgg_view("framework/db_explorer/db_column/$col", array(
					'data' => $r
						));
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
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header('Cache-Control: no-cache, must-revalidate');
header("Pragma: no-cache");
header('Content-type: application/json; charset=UTF-8');

print json_encode($results);
exit;