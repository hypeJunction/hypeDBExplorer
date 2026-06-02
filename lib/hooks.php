<?php

namespace hypeJunction\DBExplorer;

/**
 * @param mixed $hook
 * @param mixed $type
 * @param mixed $menu
 * @param mixed $params
 * @return mixed
 */
function user_hover_menu_setup($hook, $type, $menu, $params) {

	$entity = \elgg_extract('entity', $params);

	if (!$entity instanceof \ElggEntity) {
		return $menu;
	}

	$menu[] = \ElggMenuItem::factory([
		'name' => 'db_explorer',
		'text' => \elgg_echo('db_explorer:inspect'),
		'href' => 'admin/developers/db_explorer?guid=' . $entity->guid,
		'section' => 'admin',
	]);

	return $menu;
}

/**
 * @param mixed $hook
 * @param mixed $type
 * @param mixed $menu
 * @param mixed $params
 * @return mixed
 */
function entity_menu_setup($hook, $type, $menu, $params) {

	$entity = \elgg_extract('entity', $params);

	if (!$entity instanceof \ElggEntity) {
		return $menu;
	}

	$menu[] = \ElggMenuItem::factory([
		'name' => 'db_explorer',
		'text' => \elgg_echo('db_explorer:inspect'),
		'href' => 'admin/developers/db_explorer?guid=' . $entity->guid,
	]);

	return $menu;
}
