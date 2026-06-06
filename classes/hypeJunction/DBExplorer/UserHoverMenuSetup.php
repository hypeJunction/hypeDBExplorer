<?php

namespace hypeJunction\DBExplorer;

use Elgg\Event;

/**
 * Adds a "DB Explorer" item to the user hover menu for admins.
 */
class UserHoverMenuSetup {

	/**
	 * Append the DB explorer link to the user hover menu.
	 *
	 * @param Event $event "register", "menu:user_hover" event
	 *
	 * @return void
	 */
	public function __invoke(Event $event) {
		$entity = $event->getParam('entity');

		if (!$entity instanceof \ElggEntity) {
			return;
		}

		$menu = $event->getValue();
		$menu[] = \ElggMenuItem::factory([
			'name' => 'db_explorer',
			'text' => elgg_echo('db_explorer:inspect'),
			'href' => 'admin/developers/db_explorer?guid=' . $entity->guid,
			'section' => 'admin',
		]);

		return $menu;
	}
}
