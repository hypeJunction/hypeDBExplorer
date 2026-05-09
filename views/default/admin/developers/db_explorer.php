<?php

elgg_import_esm('js/framework/db_explorer');
elgg_load_external_file('css', 'db_explorer.jquery-ui');
elgg_load_external_file('css', 'db_explorer.stylesheet');

$type = get_input('type', 'user');
$guid = get_input('guid', null);
$url = urldecode(get_input('referrer_url', ''));

if (!$guid && filter_var($url, FILTER_VALIDATE_URL)) {
	$guid = \hypeJunction\DBExplorer\get_guid_from_url($url);
	if (!$guid) {
		elgg_register_error_message(elgg_echo('db_explorer:url_sniffer_no_guid', [$url]));
	}
}

if ($guid) {
	echo elgg_view('framework/db_explorer/entity', ['guid' => $guid]);
} else {
	echo elgg_view('framework/db_explorer/bulk', ['type' => $type]);
}
