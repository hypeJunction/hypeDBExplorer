<?php

namespace hypeJunction\DBExplorer;

function user_hover_menu_setup($hook, $type, $menu, $params) {

	$entity = elgg_extract('entity', $params);

	if (!$entity instanceof \ElggEntity) {
		return $menu;
	}

	$menu[] = \ElggMenuItem::factory([
		'name' => 'db_explorer',
		'text' => elgg_echo('db_explorer:inspect'),
		'href' => 'admin/developers/db_explorer?guid=' . $entity->guid,
		'section' => 'admin',
	]);

	return $menu;
}

function entity_menu_setup($hook, $type, $menu, $params) {

	$entity = elgg_extract('entity', $params);

	if (!$entity instanceof \ElggEntity) {
		return $menu;
	}

	$menu[] = \ElggMenuItem::factory([
		'name' => 'db_explorer',
		'text' => elgg_echo('db_explorer:inspect'),
		'href' => 'admin/developers/db_explorer?guid=' . $entity->guid,
	]);

	return $menu;
}
