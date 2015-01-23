<?php

$page = get_input('page', 1);

$limit = get_input('rows', 50);

$sidx = get_input('sidx', 'e.guid');
$sord = get_input('sord', 'asc');

$guid = get_input('guid', 0);

$dbprefix = elgg_get_config('dbprefix');
$row_count = get_data("SELECT COUNT(*) AS count FROM {$dbprefix}entities WHERE container_guid = $guid");
$count = $row_count[0]->count;

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

$row_data = get_data("SELECT * FROM {$dbprefix}entities e
						WHERE e.container_guid = $guid
						ORDER BY $sidx $sord
						LIMIT $limit
						OFFSET $offset"
);

if (!empty($row_data)) {

	$i = 0;

	$ordered_cols = array(
		'checkbox',
		'guid',
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

	foreach ($row_data as $r) {

		$results['rows'][$i]['id'] = $r->id;
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
