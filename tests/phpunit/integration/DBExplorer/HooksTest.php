<?php

namespace hypeJunction\DBExplorer;

use Elgg\IntegrationTestCase;
use ElggMenuItem;
use Elgg\Event;

/**
 * Locks in the behavior of the two hover/entity menu hook handlers.
 *
 * The handlers are only *registered* when an admin is logged in AND debug is on
 * (see Bootstrap::init()), but the handler classes themselves should always be
 * callable and append a 'db_explorer' menu item when given a real entity.
 */
class HooksTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'hypedbexplorer';
	}

	public function up() {}

	public function down() {}

	public function testUserHoverMenuAppendsDbExplorerItemForEntity(): void {
		$user = $this->createUser();

		$event = new Event(elgg(),'register', 'menu:user_hover', [], ['entity' => $user]);
		$handler = new UserHoverMenuSetup();
		$result = $handler($event);

		$this->assertIsArray($result);
		$this->assertCount(1, $result);
		$this->assertInstanceOf(ElggMenuItem::class, $result[0]);
		$this->assertSame('db_explorer', $result[0]->getName());
		$this->assertStringContainsString(
			'admin/developers/db_explorer?guid=' . $user->guid,
			$result[0]->getHref()
		);
	}

	public function testUserHoverMenuIgnoresNonEntity(): void {
		$event = new Event(elgg(),'register', 'menu:user_hover', [], ['entity' => null]);
		$handler = new UserHoverMenuSetup();
		$result = $handler($event);
		$this->assertNull($result);
	}

	public function testEntityMenuAppendsDbExplorerItemForEntity(): void {
		$obj = $this->createObject(['subtype' => 'test_db_explorer_obj']);

		$event = new Event(elgg(),'register', 'menu:entity', [], ['entity' => $obj]);
		$handler = new EntityMenuSetup();
		$result = $handler($event);

		$this->assertIsArray($result);
		$this->assertCount(1, $result);
		$this->assertInstanceOf(ElggMenuItem::class, $result[0]);
		$this->assertSame('db_explorer', $result[0]->getName());
		$this->assertStringContainsString(
			'admin/developers/db_explorer?guid=' . $obj->guid,
			$result[0]->getHref()
		);
	}

	public function testEntityMenuIgnoresNonEntity(): void {
		$event = new Event(elgg(),'register', 'menu:entity', [], ['entity' => 'not an entity']);
		$handler = new EntityMenuSetup();
		$result = $handler($event);
		$this->assertNull($result);
	}
}
