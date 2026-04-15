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

$entity_type = htmlspecialchars($data[0]->type ?? '', ENT_QUOTES, 'UTF-8');
$id_escaped = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');
$guid_escaped = (int) $guid;

echo '<div class="jqgrid-wrapper">';
echo "<table id=\"{$id_escaped}\" class=\"dbexplorer-grid\" data-type=\"{$entity_type}\" data-guid=\"{$guid_escaped}\" data-pager-id=\"pager-entity\"><tr><td></td></tr></table>";
echo "<div id=\"pager-entity\"></div>";
echo '</div>';
