<?php

namespace hypeJunction\DBExplorer;

use Elgg\IntegrationTestCase;

/**
 * Locks in the expected side-effects of the user-management actions.
 *
 * We do NOT invoke the action scripts through HTTP — IntegrationTestCase
 * lacks $this->executeAction(). Instead we exercise the underlying Elgg
 * primitives the actions rely on (ban/unban/disable/enable/validate) so a
 * migration cannot silently break them for the data the plugin manipulates.
 */
class UserMutationBehaviorTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'hypedbexplorer';
	}

	public function up() {}
	public function down() {}

	public function testBanUnbanLifecycle(): void {
		$admin = $this->createUser();
		$admin->makeAdmin();
		$user = $this->createUser();
		$this->assertFalse($user->isBanned());

		elgg_get_session()->setLoggedInUser($admin);
		try {
			$user->ban('test reason');
			$this->assertTrue($user->isBanned());

			$user->unban();
			$this->assertFalse($user->isBanned());
		} finally {
			elgg_get_session()->removeLoggedInUser();
		}
	}

	public function testDisableEnableLifecycle(): void {
		$admin = $this->createUser();
		$admin->makeAdmin();
		$user = $this->createUser();
		$this->assertTrue($user->isEnabled());

		elgg_get_session()->setLoggedInUser($admin);
		try {
			$user->disable('test reason');
			$this->assertFalse($user->isEnabled());

			// Re-enable requires SHOW_DISABLED context, matching the batch.php action
			elgg_call(ELGG_SHOW_DISABLED_ENTITIES, function () use ($user) {
				$reloaded = get_entity($user->guid);
				$this->assertInstanceOf(\ElggUser::class, $reloaded);
				$reloaded->enable();
			});

			elgg_call(ELGG_SHOW_DISABLED_ENTITIES, function () use ($user) {
				$reloaded = get_entity($user->guid);
				$this->assertTrue($reloaded->isEnabled());
			});
		} finally {
			elgg_get_session()->removeLoggedInUser();
		}
	}

	public function testAdminCanEditNonAdminUser(): void {
		$admin = $this->createUser();
		$admin->makeAdmin();
		$target = $this->createUser();

		elgg_get_session()->setLoggedInUser($admin);
		try {
			$this->assertTrue($target->canEdit());
		} finally {
			elgg_get_session()->removeLoggedInUser();
		}
	}

	public function testNonAdminCannotEditOtherUser(): void {
		$attacker = $this->createUser();
		$victim = $this->createUser();

		elgg_get_session()->setLoggedInUser($attacker);
		try {
			$this->assertFalse($victim->canEdit());
		} finally {
			elgg_get_session()->removeLoggedInUser();
		}
	}

	public function testValidationStatusToggle(): void {
		$user = $this->createUser();
		$user->setValidationStatus(true, 'test');
		$this->assertTrue($user->isValidated());
		$user->setValidationStatus(false, 'test');
		$this->assertFalse($user->isValidated());
	}
}
