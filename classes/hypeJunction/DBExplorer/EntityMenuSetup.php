<?php

namespace hypeJunction\DBExplorer;

use Elgg\Event;

/**
 * Adds a "DB Explorer" item to the entity menu for admins.
 */
class EntityMenuSetup {

	/**
	 * Append the DB explorer link to the entity menu.
	 *
	 * @param Event $event "register", "menu:entity" event
	 *
	 * @return array
	 */
	public function __invoke(Event $event): array {
		$menu = $event->getValue();

		$entity = $event->getParam('entity');
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
}
