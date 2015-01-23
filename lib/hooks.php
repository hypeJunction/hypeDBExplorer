<?php

namespace hypeJunction\DBExplorer;

/**
 * Adds a menu item to quickly inspect the user via the DB explorer
 *
 * @param string $hook   "register"
 * @param string $type   "menu:user_hover"
 * @param array  $menu   Menu
 * @param array  $params Hook params
 * @return array
 */
function user_hover_menu_setup($hook, $type, $menu, $params) {

	$entity = elgg_extract('entity', $params);

	if (!elgg_instanceof($entity)) {
		return $menu;
	}

	$menu['db_explorer'] = ElggMenuItem::factory(array(
				'name' => 'db_explorer',
				'text' => elgg_echo('db_explorer:inspect'),
				'href' => 'admin/developers/db_explorer?guid=' . $entity->guid,
				'section' => 'admin',
	));

	return $menu;

}

/**
 * Adds a menu item to quickly inspect the entity via the DB explorer
 *
 * @param string $hook   "register"
 * @param string $type   "menu:entity"
 * @param array  $menu   Menu
 * @param array  $params Hook params
 * @return array
 */
function entity_menu_setup($hook, $type, $menu, $params) {

	$entity = elgg_extract('entity', $params);

	if (!elgg_instanceof($entity)) {
		return $menu;
	}

	$menu['db_explorer'] = ElggMenuItem::factory(array(
				'name' => 'db_explorer',
				'text' => elgg_echo('db_explorer:inspect'),
				'href' => 'admin/developers/db_explorer?guid=' . $entity->guid,
	));

	return $menu;

}


