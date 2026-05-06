<?php

namespace hypeJunction\DBExplorer;

use Elgg\Event;

/**
 * Adds DB explorer link to entity menus.
 */
class EntityMenuSetup {

	/**
	 * @param Event $event Event
	 *
	 * @return array|null
	 */
	public function __invoke(Event $event) {
		$entity = $event->getParam('entity');

		if (!$entity instanceof \ElggEntity) {
			return;
		}

		$items = $event->getValue();
		$items[] = \ElggMenuItem::factory([
			'name' => 'db_explorer',
			'text' => elgg_echo('db_explorer:inspect'),
			'href' => 'admin/developers/db_explorer?guid=' . $entity->guid,
		]);

		return $items;
	}
}
