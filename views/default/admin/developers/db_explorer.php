<?php

elgg_load_js('jquery.jqgrid.js');
elgg_load_js('jquery.jqgrid.locale.js');
elgg_load_js('dbexplorer.jqgrid.js');

elgg_load_css('jquery.ui.css');
elgg_load_css('jquery.jqgrid.css');
elgg_load_css('dbexplorer.jqgrid.css');

$type = get_input('type', 'user');
$guid = get_input('guid', null);

if ($guid) {

	echo elgg_view('framework/db_explorer/entity', array(
		'guid' => $guid
	));

} else {

	echo elgg_view('framework/db_explorer/bulk', array(
		'type' => $type
	));

}



