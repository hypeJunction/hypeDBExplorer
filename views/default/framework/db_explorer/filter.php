<?php

$filter_context = elgg_extract('filter_context', $vars);

$tabs = array(
	'user' => array(
		'text' => elgg_echo('item:user'),
		'href' => 'admin/developers/db_explorer?type=user',
		'selected' => ($filter_context == 'user'),
		'priority' => 100,
	),
	'object' => array(
		'text' => elgg_echo('hj:db_explorer:objects'),
		'href' => 'admin/developers/db_explorer?type=object',
		'selected' => ($filter_context == 'object'),
		'priority' => 200,
	),
	'group' => array(
		'text' => elgg_echo('item:group'),
		'href' => 'admin/developers/db_explorer?type=group',
		'selected' => ($filter_context == 'group'),
		'priority' => 300,
	),
	'site' => array(
		'text' => elgg_echo('item:site'),
		'href' => 'admin/developers/db_explorer?type=site',
		'selected' => ($filter_context == 'site'),
		'priority' => 400,
	),
);

if ($filter_context == 'entity') {
	$tabs['entity'] = array(
		'text' => elgg_echo('hj:db_explorer:entity'),
		'href' => '#',
		'selected' => true,
		'priority' => 50
	);
}

foreach ($tabs as $name => $tab) {
	if ($tab) {
		$tab['name'] = $name;
		elgg_register_menu_item('filter', $tab);
	}
}

echo elgg_view_menu('filter', array('sort_by' => 'priority', 'class' => 'elgg-menu-hz elgg-tabs'));
