<?php

namespace hypeJunction\DBExplorer;

use Elgg\Event;

class EntityMenuSetup {

	public function __invoke(Event $event): void {
		$entity = $event->getParam('entity');

		if (!$entity instanceof \ElggEntity) {
			return;
		}

		$menu = $event->getValue();
		$menu->add(\ElggMenuItem::factory([
			'name' => 'db_explorer',
			'text' => elgg_echo('db_explorer:inspect'),
			'href' => 'admin/developers/db_explorer?guid=' . $entity->guid,
		]));
	}
}
