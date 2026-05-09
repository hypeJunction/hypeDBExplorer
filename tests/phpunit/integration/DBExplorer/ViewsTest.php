<?php

namespace hypeJunction\DBExplorer;

use Elgg\IntegrationTestCase;

/**
 * Smoke-tests that critical plugin views exist and render without fatal errors.
 * Behavior lock: the migration must preserve these views.
 */
class ViewsTest extends IntegrationTestCase {

	/**
     * @return string
     */
    public function getPluginID(): string {
		return 'hypedbexplorer';
	}

	public function up() {}
	public function down() {}

	/**
     * @return array
     */
    public function criticalViews(): array {
		return [
			['admin/developers/db_explorer'],
			['forms/db_explorer/batch'],
			['framework/db_explorer/bulk'],
			['framework/db_explorer/entity'],
			['framework/db_explorer/filter'],
			['framework/db_explorer/db_column/guid'],
			['framework/db_explorer/db_column/type'],
			['framework/db_explorer/db_column/subtype'],
			['framework/db_explorer/db_column/owner_guid'],
			['framework/db_explorer/db_column/container_guid'],
			['framework/db_explorer/db_column/access_id'],
			['framework/db_explorer/db_column/time_created'],
			['framework/db_explorer/db_column/time_updated'],
			['framework/db_explorer/db_column/enabled'],
			['framework/db_explorer/db_column/checkbox'],
		];
	}

	/**
	 * @dataProvider criticalViews
	 */
	public function testViewExists(string $view): void {
		$this->assertTrue(
			elgg_view_exists($view),
			"View {$view} should exist"
		);
	}

	/**
     * @return void
     */
    public function testAdminPageRenders(): void {
		$admin = $this->createUser();
		$admin->makeAdmin();
		_elgg_services()->session_manager->setLoggedInUser($admin);

		try {
			$output = elgg_view('admin/developers/db_explorer', []);
			$this->assertIsString($output);
			$this->assertNotEmpty($output);
		} finally {
			_elgg_services()->session_manager->removeLoggedInUser();
		}
	}

	/**
     * @return void
     */
    public function testFilterViewRendersWithEntityType(): void {
		$output = elgg_view('framework/db_explorer/filter', ['entity_type' => 'object']);
		$this->assertIsString($output);
	}

	/**
     * @return void
     */
    public function testGuidColumnRendersWithEntityData(): void {
		$user = $this->createUser();
		$row = (object) [
			'guid' => $user->guid,
			'type' => 'user',
			'subtype' => null,
		];
		$output = elgg_view('framework/db_explorer/db_column/guid', ['data' => $row]);
		$this->assertIsString($output);
		$this->assertStringContainsString((string) $user->guid, $output);
	}
}
