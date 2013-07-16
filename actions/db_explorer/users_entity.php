<?php

$page = get_input('page', 1);

$limit = get_input('rows', 50);

$sidx = get_input('sidx', 'ue.guid');
$sord = get_input('sord', 'asc');

$guid = get_input('guid', 0);

$dbprefix = elgg_get_config('dbprefix');
$count = 1;
$total_pages = 1;
$offset = 0;

$row_data = get_data("SELECT * FROM {$dbprefix}users_entity ue WHERE ue.guid = $guid ORDER BY $sidx $sord LIMIT $limit OFFSET $offset");

if (!empty($row_data)) {

	$i = 0;

	$ordered_cols = array(
		'guid',
		'name',
		'username',
		'email',
		'language',
		'banned',
		'admin',
		'last_action',
		'prev_last_action',
		'last_login',
		'prev_last_login',
	);

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