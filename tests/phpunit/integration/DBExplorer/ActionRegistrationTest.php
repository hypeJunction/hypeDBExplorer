<?php

namespace hypeJunction\DBExplorer;

use Elgg\IntegrationTestCase;

/**
 * Verifies that hypeDBExplorer registers all actions and — CRITICALLY — that
 * listing (data-dump) actions are gated to 'admin' access.
 *
 * The point of these tests is to lock in the current security posture so that
 * a migration cannot silently loosen access to an SQL-heavy admin tool.
 */
class ActionRegistrationTest extends IntegrationTestCase {

	/**
     * @return string
     */
    public function getPluginID(): string {
		return 'hypedbexplorer';
	}

	public function up() {
		// nothing
	}

	public function down() {
		// nothing
	}

	/**
	 * Actions that dump raw DB contents MUST be admin-only.
	 */
	public function adminListingActions(): array {
		return [
			['db_explorer/entities'],
			['db_explorer/owned_entities'],
			['db_explorer/contained_entities'],
			['db_explorer/river_items'],
			['db_explorer/metadata'],
			['db_explorer/metadata_ownership'],
			['db_explorer/annotations'],
			['db_explorer/annotations_ownership'],
			['db_explorer/private_settings'],
			['db_explorer/access_collections_ownership'],
			['db_explorer/access_collections_membership'],
			['db_explorer/entity_relationships'],
		];
	}

	/**
	 * @dataProvider adminListingActions
	 */
	public function testAdminListingActionIsRegisteredWithAdminAccess(string $action): void {
		$this->assertTrue(
			elgg_action_exists($action),
			"Action {$action} should be registered"
		);

		$actions_service = _elgg_services()->actions;
		$all = $actions_service->getAllActions();
		$this->assertArrayHasKey($action, $all, "Action {$action} missing from registry");
		$this->assertSame(
			'admin',
			$all[$action]['access'],
			"Action {$action} MUST require admin access (currently: {$all[$action]['access']})"
		);
	}

	/**
	 * These action families perform mutations; behavior-preserving migration
	 * must keep them registered (even if on a non-admin access level, since
	 * the originals rely on canEdit() for authorization).
	 */
	public function mutationActions(): array {
		return [
			['db_explorer/batch'],
			['db_explorer/user/validate'],
			['db_explorer/user/ban'],
			['db_explorer/user/unban'],
			['db_explorer/user/enable'],
			['db_explorer/user/disable'],
			['db_explorer/user/delete'],
			['db_explorer/content/enable'],
			['db_explorer/content/disable'],
			['db_explorer/content/delete'],
		];
	}

	/**
	 * @dataProvider mutationActions
	 */
	public function testMutationActionIsRegistered(string $action): void {
		$this->assertTrue(
			elgg_action_exists($action),
			"Mutation action {$action} should be registered"
		);
	}
}
