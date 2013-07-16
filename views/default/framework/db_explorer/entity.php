<?php

$guid = elgg_extract('guid', $vars, 0);

if (!elgg_is_xhr()) {
	echo elgg_view('framework/db_explorer/filter', array(
		'filter_context' => 'entity'
	));
	$id = "dbexplorer-entity-$guid";
} else {
	$id = "dbexplorer-popup-entity-$guid";
}

$dbprefix = elgg_get_config('dbprefix');
$data = get_data("SELECT type FROM {$dbprefix}entities WHERE guid = $guid");

$attr = elgg_format_attributes(array(
	'id' => $id,
	'class' => 'dbexplorer-grid',
	'data-type' => $data[0]->type,
	'data-guid' => $guid,
	'data-pager-id' => 'pager-entity'
));

echo '<div class="jqgrid-wrapper">';
echo "<table $attr><tr><td></td></tr></table>";
echo "<div id=\"pager-entity\"></div>";
echo '</div>';

