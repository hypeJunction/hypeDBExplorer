<?php

$guid = elgg_extract('guid', $vars, 0);

if (!elgg_is_xhr()) {
	echo elgg_view('framework/db_explorer/filter', [
		'filter_context' => 'entity',
	]);
	$id = "dbexplorer-entity-$guid";
} else {
	$id = "dbexplorer-popup-entity-$guid";
}

$qb = \Elgg\Database\Select::fromTable('entities');
$qb->select('type')
	->where($qb->compare('guid', '=', (int) $guid, ELGG_VALUE_INTEGER));
$data = elgg()->db->getData($qb);

$attr = elgg_format_attributes([
	'id' => $id,
	'class' => 'dbexplorer-grid',
	'data-type' => $data[0]->type,
	'data-guid' => $guid,
	'data-pager-id' => 'pager-entity',
]);

echo '<div class="jqgrid-wrapper">';
echo "<table $attr><tr><td></td></tr></table>";
echo "<div id=\"pager-entity\"></div>";
echo '</div>';
