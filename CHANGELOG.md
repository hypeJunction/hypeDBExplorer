# Changelog

## [5.0.0] — Elgg 5.x migration

### Breaking changes
- Minimum Elgg version is now 5.0; minimum PHP is 8.2
- Hook system merged into events: `elgg_register_plugin_hook_handler()` → `elgg_register_event_handler()`
- Handler classes now type-hint `\Elgg\Event` instead of `\Elgg\Hook`

### Changed
- `elgg-plugin.php` version bumped to 5.0.0
- `UserHoverMenuSetup`, `EntityMenuSetup`: `use Elgg\Hook` → `use Elgg\Event`
- `Bootstrap::init()`: `elgg_register_plugin_hook_handler()` → `elgg_register_event_handler()`
- `languages/en.php`: `add_translation('en', $array)` removed; file returns array directly
- Docker stack updated: PHP 7.4→8.2, MySQL 5.7→8.0, Elgg ~5.1.x, Playwright 1.49→1.59
- Tests adapted for Elgg 5.x API changes (session_manager, void delete, null get_entity)

## [4.0.0] — Elgg 4.x migration

### Breaking changes
- Minimum Elgg version is now 4.0
- Plugin ID is now `hypedbexplorer` (lowercase); any code using `hypeDBExplorer` as the ID must be updated
- `manifest.xml` removed; `composer.json` is the sole plugin metadata source

### Added
- `elgg-plugin.php` — Elgg 4.x plugin manifest
- `classes/hypeJunction/DBExplorer/Bootstrap.php` — Plugin bootstrap class
- `classes/hypeJunction/DBExplorer/UserHoverMenuSetup.php` — Elgg 4.x hook handler class
- `classes/hypeJunction/DBExplorer/EntityMenuSetup.php` — Elgg 4.x hook handler class
- PSR-4 autoloading via `composer.json`

### Changed
- `start.php` removed; bootstrap logic moved to `Bootstrap::init()`
- Hook handlers converted from 4-arg functions to invokable classes using `\Elgg\Hook`
- `elgg_register_css()` → `elgg_register_external_file('css', ...)`
- `elgg_load_css()` → `elgg_load_external_file('css', ...)`
- `elgg_format_attributes()` replaced with inline HTML escaping in bulk/entity views
- `get_current_language()` → `elgg_get_current_language()`
- All 6 PHPUnit integration tests adapted for Elgg 4.x:
  - `elgg_get_session()->setLoggedInUser()` preserved (still valid in 4.x)
  - Plugin ID references updated from `hypeDBExplorer` to `hypedbexplorer`
  - Hook tests updated to use `\Elgg\HooksRegistrationService\Hook` and class-based handlers
  - ban/disable lifecycle tests now run in an admin session context

## [3.2.0]

Legacy release for Elgg 3.x.
