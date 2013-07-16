<?php

$type = elgg_extract('type', $vars);

if (!elgg_is_xhr()) {
	echo elgg_view('framework/db_explorer/filter', array(
		'filter_context' => $type
	));
}

$attr = elgg_format_attributes(array(
	'id' => "dbexplorer-$type",
	'class' => 'dbexplorer-grid',
	'data-type' => $type,
	'data-pager-id' => "pager-$type"
));

echo '<div class="jqgrid-wrapper">';
echo "<table $attr><tr><td></td></tr></table>";
echo "<div id=\"pager-$type\"></div>";
echo '</div>';