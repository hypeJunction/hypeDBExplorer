<?php

namespace hypeJunction\DBExplorer;

use Elgg\IntegrationTestCase;
use ElggMenuItem;

/**
 * Locks in the behavior of the two hover/entity menu hooks.
 *
 * The hooks are only *registered* when an admin is logged in AND debug is on
 * (see start.php), but the handler functions themselves should always be
 * callable and append a 'db_explorer' menu item when given a real entity.
 */
class HooksTest extends IntegrationTestCase {

    public function getPluginID(): string {
        return 'hypeDBExplorer';
    }

    public function up() {
        if (!function_exists(__NAMESPACE__ . '\\user_hover_menu_setup')) {
            require_once dirname(__DIR__, 4) . '/lib/hooks.php';
        }
    }

    public function down() {}

    public function testUserHoverMenuAppendsDbExplorerItemForEntity(): void {
        $user = $this->createUser();

        $menu = [];
        $result = user_hover_menu_setup('register', 'menu:user_hover', $menu, [
            'entity' => $user,
        ]);

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
        $menu = ['existing'];
        $result = user_hover_menu_setup('register', 'menu:user_hover', $menu, [
            'entity' => null,
        ]);
        $this->assertSame($menu, $result);
    }

    public function testEntityMenuAppendsDbExplorerItemForEntity(): void {
        $obj = $this->createObject(['subtype' => 'test_db_explorer_obj']);

        $menu = [];
        $result = entity_menu_setup('register', 'menu:entity', $menu, [
            'entity' => $obj,
        ]);

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
        $menu = [];
        $result = entity_menu_setup('register', 'menu:entity', $menu, [
            'entity' => 'not an entity',
        ]);
        $this->assertSame($menu, $result);
    }
}
