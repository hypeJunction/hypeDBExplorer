<?php

namespace hypeJunction\DBExplorer;

use Elgg\IntegrationTestCase;
use ElggMenuItem;
use Elgg\Event;
use Elgg\Menu\MenuItems;

class HooksTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'hypedbexplorer';
	}

	public function up() {}

	public function down() {}

	public function testUserHoverMenuAppendsDbExplorerItemForEntity(): void {
		$user = $this->createUser();
		$menu = new MenuItems();

		$event = new Event(elgg(), 'register', 'menu:user_hover', $menu, ['entity' => $user]);
		$handler = new UserHoverMenuSetup();
		$handler($event);

		$items = $menu->all();
		$this->assertCount(1, $items);
		$item = array_values($items)[0];
		$this->assertInstanceOf(ElggMenuItem::class, $item);
		$this->assertSame('db_explorer', $item->getName());
		$this->assertStringContainsString(
			'admin/developers/db_explorer?guid=' . $user->guid,
			$item->getHref()
		);
	}

	public function testUserHoverMenuIgnoresNonEntity(): void {
		$menu = new MenuItems();
		$event = new Event(elgg(), 'register', 'menu:user_hover', $menu, ['entity' => null]);
		$handler = new UserHoverMenuSetup();
		$handler($event);

		$this->assertCount(0, $menu->all());
	}

	public function testEntityMenuAppendsDbExplorerItemForEntity(): void {
		$obj = $this->createObject(['subtype' => 'test_db_explorer_obj']);
		$menu = new MenuItems();

		$event = new Event(elgg(), 'register', 'menu:entity', $menu, ['entity' => $obj]);
		$handler = new EntityMenuSetup();
		$handler($event);

		$items = $menu->all();
		$this->assertCount(1, $items);
		$item = array_values($items)[0];
		$this->assertInstanceOf(ElggMenuItem::class, $item);
		$this->assertSame('db_explorer', $item->getName());
		$this->assertStringContainsString(
			'admin/developers/db_explorer?guid=' . $obj->guid,
			$item->getHref()
		);
	}

	public function testEntityMenuIgnoresNonEntity(): void {
		$menu = new MenuItems();
		$event = new Event(elgg(), 'register', 'menu:entity', $menu, ['entity' => 'not an entity']);
		$handler = new EntityMenuSetup();
		$handler($event);

		$this->assertCount(0, $menu->all());
	}
}
