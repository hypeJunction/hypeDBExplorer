<?php

namespace hypeJunction\DBExplorer;

use Elgg\Event;

class EntityMenuSetup {

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
