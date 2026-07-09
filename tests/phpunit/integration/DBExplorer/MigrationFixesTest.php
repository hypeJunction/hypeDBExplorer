<?php

namespace hypeJunction\DBExplorer;

use Elgg\IntegrationTestCase;

/**
 * Regression tests for the Elgg 7.x migration fixes applied to hypeDBExplorer.
 *
 * Each test pins one migration-fix commit so a future automated sweep cannot
 * silently re-break the fixed behaviour.
 */
class MigrationFixesTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'hypedbexplorer';
	}

	public function up() {}

	public function down() {}

	private function pluginRoot(): string {
		return dirname(__DIR__, 4);
	}

	private function importMap(): array {
		return _elgg_services()->esm->getImportMapData()['imports'] ?? [];
	}

	/**
	 * 100dfe4: jqgrid was registered via elgg_get_simplecache_url() of a
	 * non-existent framework/db_explorer/jqgrid.js view, so it never entered the
	 * Elgg 7 importmap and `import 'jqgrid'` in db_explorer.mjs aborted. It must
	 * now resolve to the vendored bundle by absolute normalized URL.
	 */
	public function testJqgridEsmResolvesToVendoredBundleNotSimplecacheView(): void {
		$imports = $this->importMap();

		$this->assertArrayHasKey(
			'jqgrid',
			$imports,
			"Bootstrap::init() must elgg_register_esm('jqgrid', ...) so `import 'jqgrid'` resolves on Elgg 7"
		);
		$this->assertStringContainsString(
			'vendors/jqgrid/js/minified/jquery.jqGrid.min.js',
			(string) $imports['jqgrid'],
			'jqgrid must map to the vendored min bundle, not a framework/db_explorer/jqgrid.js simplecache view'
		);
		$this->assertStringNotContainsString(
			'framework/db_explorer/jqgrid',
			(string) $imports['jqgrid'],
			'jqgrid must NOT point at the removed simplecache jqgrid view'
		);
		$this->assertFileExists(
			$this->pluginRoot() . '/vendors/jqgrid/js/minified/jquery.jqGrid.min.js',
			'The vendored jqGrid bundle the importmap points at must ship on disk'
		);
	}

	/**
	 * 100dfe4 + 773e3b8: the locale bundle is chosen for the current language and
	 * falls back to grid.locale-en.js when that locale file is absent. Assert the
	 * specifier is registered to a real vendored locale file, and the English
	 * fallback target exists on disk.
	 */
	public function testJqgridLocaleEsmRegisteredWithEnglishFallbackOnDisk(): void {
		$imports = $this->importMap();

		$this->assertArrayHasKey('jqgrid.locale', $imports, "Bootstrap must register the 'jqgrid.locale' specifier");
		$this->assertStringContainsString(
			'vendors/jqgrid/js/i18n/grid.locale-',
			(string) $imports['jqgrid.locale'],
			'jqgrid.locale must map to a vendored grid.locale-{lang}.js bundle'
		);
		$this->assertFileExists(
			$this->pluginRoot() . '/vendors/jqgrid/js/i18n/grid.locale-en.js',
			'grid.locale-en.js must ship — it is the fallback when a language locale file is missing'
		);
	}

	/**
	 * fdcc703: the module was renamed js/framework/db_explorer.js -> .mjs because
	 * Elgg 7 only registers .mjs files in the importmap. The specifier must
	 * resolve and no legacy .js twin may remain.
	 */
	public function testDbExplorerModuleIsImportedAsEsmNotAmd(): void {
		$imports = $this->importMap();

		$this->assertArrayHasKey(
			'js/framework/db_explorer',
			$imports,
			'js/framework/db_explorer.mjs must auto-register in the Elgg 7 importmap'
		);
		$this->assertTrue(
			elgg_view_exists('js/framework/db_explorer.mjs'),
			'The ESM view js/framework/db_explorer.mjs must exist'
		);
		$this->assertFileDoesNotExist(
			$this->pluginRoot() . '/views/default/js/framework/db_explorer.js',
			'The legacy AMD db_explorer.js must be gone after the .mjs rename'
		);
	}

	/**
	 * 100dfe4: elgg/i18n and elgg/security are read-only namespace objects on
	 * Elgg 7, so `import * as i18n` broke i18n.echo()/security.addToken() and
	 * `elgg.db_explorer = {}`. The module must use default imports.
	 */
	public function testDbExplorerMjsUsesDefaultImportsForElggNamespaces(): void {
		$src = (string) file_get_contents($this->pluginRoot() . '/views/default/js/framework/db_explorer.mjs');

		$this->assertMatchesRegularExpression(
			"/import\s+elgg\s+from\s+['\"]elgg['\"]/",
			$src,
			"db_explorer.mjs must default-import elgg so `elgg.db_explorer = {}` works"
		);
		$this->assertMatchesRegularExpression(
			"/import\s+i18n\s+from\s+['\"]elgg\/i18n['\"]/",
			$src,
			"db_explorer.mjs must default-import i18n (i18n.echo is a method on the default export)"
		);
		$this->assertMatchesRegularExpression(
			"/import\s+security\s+from\s+['\"]elgg\/security['\"]/",
			$src,
			"db_explorer.mjs must default-import security (security.addToken)"
		);
		$this->assertDoesNotMatchRegularExpression(
			"/import\s*\*\s*as\s+\w+\s+from\s+['\"]elgg(\/(i18n|security))?['\"]/",
			$src,
			'db_explorer.mjs must NOT use `import * as` for read-only elgg namespaces on Elgg 7'
		);
	}

	/**
	 * 7a02344: the git-tracked global helper get_guid_from_url() must load before
	 * Bootstrap, so elgg-plugin.php require_once's lib/functions.php at the top
	 * (composer autoload.files does not cover git-tracked customs). A later
	 * render-fix port had clobbered this line.
	 */
	public function testGlobalHelperRequiredAtTopOfManifest(): void {
		$src = (string) file_get_contents($this->pluginRoot() . '/elgg-plugin.php');

		$requirePos = strpos($src, "require_once __DIR__ . '/lib/functions.php'");
		$this->assertNotFalse($requirePos, 'elgg-plugin.php must require_once lib/functions.php');

		$returnPos = strpos($src, 'return [');
		$this->assertNotFalse($returnPos, 'elgg-plugin.php must return the config array');
		$this->assertLessThan(
			$returnPos,
			$requirePos,
			'lib/functions.php must be required BEFORE the config array (before Bootstrap loads)'
		);

		$this->assertTrue(
			function_exists(__NAMESPACE__ . '\\get_guid_from_url'),
			'get_guid_from_url() must be defined once the plugin has loaded'
		);
	}

	/**
	 * 847b37c: a composer.json "version" field shadows the git tag at install
	 * time. It must be absent so the tag drives the installed version.
	 */
	public function testComposerJsonHasNoVersionField(): void {
		$composer = json_decode((string) file_get_contents($this->pluginRoot() . '/composer.json'), true);

		$this->assertIsArray($composer);
		$this->assertArrayNotHasKey(
			'version',
			$composer,
			'composer.json version field shadows the git tag — it must not be present'
		);

		$config = include $this->pluginRoot() . '/elgg-plugin.php';
		$this->assertNotEmpty($config['plugin']['version'], 'elgg-plugin.php must still declare a plugin version');
	}

	/**
	 * 773e3b8 / bde2986 / 0abc7c9: the jqgrid locale is chosen with
	 * elgg_get_current_language(). The legacy get_current_language() (removed 4.x),
	 * the wrong elgg_get_language(), and the double-prefixed elgg_elgg_get_language()
	 * from a bad automated rename must all be absent from Bootstrap.
	 */
	public function testBootstrapSelectsLocaleWithCurrentLanguageHelper(): void {
		$src = (string) file_get_contents($this->pluginRoot() . '/classes/hypeJunction/DBExplorer/Bootstrap.php');

		$this->assertStringContainsString(
			'elgg_get_current_language()',
			$src,
			'Bootstrap must select the jqgrid locale via elgg_get_current_language() on Elgg 7'
		);
		$this->assertDoesNotMatchRegularExpression(
			'/(?<![\w>$:\\\\])get_current_language\s*\(/',
			$src,
			'get_current_language() was removed in 4.x'
		);
		$this->assertDoesNotMatchRegularExpression(
			'/(?<![\w>$:\\\\])elgg_get_language\s*\(/',
			$src,
			'elgg_get_language() is not the 7.x language helper'
		);
		$this->assertStringNotContainsString(
			'elgg_elgg_get_language',
			$src,
			'elgg_elgg_get_language() is a bad double-prefixed rename artifact'
		);
	}
}
