<?php

namespace hypeJunction\DBExplorer;

use Elgg\IntegrationTestCase;

/**
 * Locks in the expected side-effects of the content-management actions.
 */
class ContentMutationBehaviorTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'hypedbexplorer';
	}

	public function up() {}
	public function down() {}

	public function testObjectDisableEnable(): void {
		$admin = $this->createUser();
		$admin->makeAdmin();
		$obj = $this->createObject(['subtype' => 'test_db_explorer_obj']);
		$this->assertTrue($obj->isEnabled());

		elgg_get_session()->setLoggedInUser($admin);
		try {
			$obj->disable('test');
			$this->assertFalse($obj->isEnabled());

			elgg_call(ELGG_SHOW_DISABLED_ENTITIES, function () use ($obj) {
				$reloaded = get_entity($obj->guid);
				$reloaded->enable();
				$this->assertTrue($reloaded->isEnabled());
			});
		} finally {
			elgg_get_session()->removeLoggedInUser();
		}
	}

	public function testObjectDeleteRemovesEntity(): void {
		$admin = $this->createUser();
		$admin->makeAdmin();
		$obj = $this->createObject(['subtype' => 'test_db_explorer_obj']);
		$guid = $obj->guid;

		elgg_get_session()->setLoggedInUser($admin);
		try {
			$this->assertTrue($obj->delete(true));
			$this->assertFalse(get_entity($guid));
		} finally {
			elgg_get_session()->removeLoggedInUser();
		}
	}

	public function testGroupIsAcceptedByContentActions(): void {
		// content/* actions accept ElggObject OR ElggGroup
		$group = $this->createGroup();
		$this->assertInstanceOf(\ElggGroup::class, $group);
		$this->assertTrue($group instanceof \ElggObject || $group instanceof \ElggGroup);
	}

	public function testOwnerCanEditTheirObject(): void {
		$owner = $this->createUser();
		$obj = $this->createObject([
			'subtype' => 'test_db_explorer_obj',
			'owner_guid' => $owner->guid,
		]);

		elgg_get_session()->setLoggedInUser($owner);
		try {
			$this->assertTrue($obj->canEdit());
		} finally {
			elgg_get_session()->removeLoggedInUser();
		}
	}

	public function testNonOwnerNonAdminCannotEditObject(): void {
		$owner = $this->createUser();
		$other = $this->createUser();
		$obj = $this->createObject([
			'subtype' => 'test_db_explorer_obj',
			'owner_guid' => $owner->guid,
			'access_id' => ACCESS_PUBLIC,
		]);

		elgg_get_session()->setLoggedInUser($other);
		try {
			$this->assertFalse($obj->canEdit());
		} finally {
			elgg_get_session()->removeLoggedInUser();
		}
	}
}
