<?php

/**
 * Displays a grid of DB entries
 *
 * @uses $_GET['type'] Optional. Type of entities to be inspected
 * @uses $_GET['guid'] Optional. GUID of an entity to be explored
 * @uses $_GET['referrer_url'] Optional. URL to sniff for an entity GUID to be explored
 */
elgg_require_js('framework/db_explorer');

//elgg_load_css('jquery.jqgrid');
elgg_load_css('db_explorer.jquery-ui');
elgg_load_css('db_explorer.stylesheet');

$type = get_input('type', 'user');
$guid = get_input('guid', null);
$url = urldecode(get_input('referrer_url', ''));

if (!$guid && filter_var($url, FILTER_VALIDATE_URL)) {

	elgg_load_library('db_explorer');
	$guid = hj_db_explorer_get_guid_from_url($url);

	if (!$guid) {
		register_error(elgg_echo('db_explorer:url_sniffer_no_guid', array($url)));
	}
}

if ($guid) {

	echo elgg_view('framework/db_explorer/entity', array(
		'guid' => $guid
	));
} else {

	echo elgg_view('framework/db_explorer/bulk', array(
		'type' => $type
	));
}



