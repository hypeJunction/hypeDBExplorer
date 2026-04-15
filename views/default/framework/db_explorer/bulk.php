<?php

$type = elgg_extract('type', $vars);

if (!elgg_is_xhr()) {
	echo elgg_view('framework/db_explorer/filter', [
		'filter_context' => $type,
	]);
}

$type_escaped = htmlspecialchars($type, ENT_QUOTES, 'UTF-8');

echo '<div class="jqgrid-wrapper">';
echo "<table id=\"dbexplorer-{$type_escaped}\" class=\"dbexplorer-grid\" data-type=\"{$type_escaped}\" data-pager-id=\"pager-{$type_escaped}\"><tr><td></td></tr></table>";
echo "<div id=\"pager-{$type_escaped}\"></div>";
echo '</div>';

echo elgg_view_form('db_explorer/batch', [
	'class' => 'js-dbexplorer-batch-form',
]);
