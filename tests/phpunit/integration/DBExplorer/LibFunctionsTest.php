<?php

namespace hypeJunction\DBExplorer;

use Elgg\IntegrationTestCase;

/**
 * Locks in the behavior of the (deprecated) library helpers.
 */
class LibFunctionsTest extends IntegrationTestCase {

    public function getPluginID(): string {
        return 'hypeDBExplorer';
    }

    public function up() {
        $funcFile = dirname(__DIR__, 4) . '/lib/functions.php';
        $deprFile = dirname(__DIR__, 4) . '/lib/deprecated.php';
        if (!function_exists(__NAMESPACE__ . '\\get_guid_from_url')) {
            require_once $funcFile;
        }
        if (!function_exists('hj_db_explorer_get_guid_from_url')) {
            require_once $deprFile;
        }
    }

    public function down() {}

    public function testGetGuidFromUrlReturnsZero(): void {
        // The namespaced function is deprecated — it returns 0 unconditionally.
        $this->assertSame(0, get_guid_from_url('/anything/123'));
        $this->assertSame(0, get_guid_from_url(''));
    }

    public function testLegacyWrapperDelegatesToNamespacedFunction(): void {
        $this->assertSame(0, \hj_db_explorer_get_guid_from_url('/anything/123'));
    }
}
