<?php

namespace hypeJunction\DBExplorer;

use Elgg\Hook;

class EntityMenuSetup {

	public function __invoke(Hook $hook) {
		$entity = $hook->getParam('entity');

		if (!$entity instanceof \ElggEntity) {
			return;
		}

		$items = $hook->getValue();
		$items[] = \ElggMenuItem::factory([
			'name' => 'db_explorer',
			'text' => elgg_echo('db_explorer:inspect'),
			'href' => 'admin/developers/db_explorer?guid=' . $entity->guid,
		]);

		return $items;
	}
}
