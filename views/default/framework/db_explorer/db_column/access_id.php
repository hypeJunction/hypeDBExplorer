<?php

$data = elgg_extract('data', $vars);
$access_id = $data->access_id;

$access_array = array(
	ACCESS_PRIVATE => elgg_echo("PRIVATE"),
	ACCESS_FRIENDS => elgg_echo("access:friends:label"),
	ACCESS_LOGGED_IN => elgg_echo("LOGGED_IN"),
	ACCESS_PUBLIC => elgg_echo("PUBLIC")
);

if (array_key_exists($access_id, $access_array)) {
	echo $access_array[$access_id];
	return;
}

$collection = get_access_collection($access_id);

if ($collection) {
	echo elgg_view('output/url', array(
		'text' => $collection->name,
		'href' => "admin/developers/db_explorer?acl=$collection->id"
	));
	return;
}

echo $access_id;
