# hypeDBExplorer — Architecture (Elgg 6.x)

## What it does

hypeDBExplorer is an admin-only database inspection and bulk-management tool for Elgg. It surfaces raw entity, metadata, annotation, relationship, and river data in a grid view (jqGrid), and provides bulk-action scripts for managing users (ban, unban, disable, enable, delete, validate) and content (enable, disable, delete).

Access to all data-listing actions is gated to the `admin` access level. Mutation actions rely on Elgg's standard `canEdit()` authorization.

## Directory structure

```
hypedbexplorer/
├── actions/db_explorer/          # Action scripts (PHP)
│   ├── *.php                     # Listing/dump actions (admin access)
│   ├── user/                     # User mutation actions
│   └── content/                  # Content mutation actions
├── classes/hypeJunction/DBExplorer/
│   ├── Bootstrap.php             # Plugin bootstrap (DefaultPluginBootstrap)
│   ├── EntityMenuSetup.php       # Hook handler: entity menu item
│   └── UserHoverMenuSetup.php    # Hook handler: user hover menu item
├── docker/                       # Per-plugin Elgg 4.x test stack
├── languages/                    # i18n strings
├── lib/                          # Legacy helper functions (deprecated)
│   ├── deprecated.php            # hj_db_explorer_get_guid_from_url()
│   ├── functions.php             # get_guid_from_url() — returns 0
│   └── hooks.php                 # Legacy hook functions (unused in 4.x)
├── tests/
│   ├── phpunit/integration/DBExplorer/   # 6 PHPUnit integration test classes
│   └── playwright/               # Playwright E2E tests
├── views/default/                # View files
│   ├── admin/developers/db_explorer.php  # Main admin page view
│   ├── forms/db_explorer/batch.php       # Batch action form
│   ├── framework/db_explorer/            # Grid and filter views
│   └── js/framework/db_explorer.js       # Frontend JavaScript
├── composer.json
└── elgg-plugin.php
```

## Bootstrap

`hypeJunction\DBExplorer\Bootstrap` (extends `Elgg\DefaultPluginBootstrap`) registers everything in its `init()` method:

- **Actions** — 12 admin-only listing actions + 10 mutation actions via `elgg_register_action()`
- **JS** — `jqgrid` and locale-aware `jqgrid.locale` modules via `elgg_register_esm()`
- **CSS** — `db_explorer.jquery-ui` and `db_explorer.stylesheet` via `elgg_register_external_file()`
- **Menus** — page menu item `admin/developers/db_explorer` (admin + debug mode only)
- **Hook handlers** — `menu:user_hover` and `menu:entity` hooks (admin + debug mode only)

## Hook handlers

| Class | Hook | Type | Behavior |
|---|---|---|---|
| `UserHoverMenuSetup` | `register` | `menu:user_hover` | Appends "DB Explorer" link to user hover menu |
| `EntityMenuSetup` | `register` | `menu:entity` | Appends "DB Explorer" link to entity action menu |

Both handlers use the Elgg 5.x single-arg `\Elgg\Event $event` signature.

## Actions

### Admin-only listing actions (access: `admin`)
`db_explorer/entities`, `owned_entities`, `contained_entities`, `river_items`, `metadata`, `metadata_ownership`, `annotations`, `annotations_ownership`, `private_settings`, `access_collections_ownership`, `access_collections_membership`, `entity_relationships`

### Mutation actions (access: logged_in, authorized by canEdit())
`db_explorer/batch`, `user/validate`, `user/ban`, `user/unban`, `user/enable`, `user/disable`, `user/delete`, `content/enable`, `content/disable`, `content/delete`

## Views

- `admin/developers/db_explorer` — main admin page (loads JS/CSS, renders filter + grid)
- `framework/db_explorer/bulk` — entity-type grid view
- `framework/db_explorer/entity` — single-entity grid view
- `framework/db_explorer/filter` — entity type/subtype filter form
- `framework/db_explorer/db_column/*` — individual column renderers (guid, type, subtype, owner, access, timestamps, etc.)
- `framework/db_explorer/notifications/*` — bulk-action result notifications
- `forms/db_explorer/batch` — batch action form

## Routes

None. The plugin is accessed via `/admin/developers/db_explorer` (standard Elgg admin routing).

## Entities

No custom entity types or subtypes. The plugin reads from Elgg's standard `entities` table and related tables.

## Custom DB tables

None. This plugin has no custom database tables and requires no data migration.

## Dependencies

No Elgg plugin dependencies. Requires Elgg ≥ 6.0, PHP ≥ 8.2.

## Migration notes (5.x → 6.x)

- AMD JS removed: `db_explorer.js` converted from `define([...], function(){...})` to ES module.
- `elgg_require_js()` → `elgg_import_esm()` in admin view.
- `elgg_define_js()` → `elgg_register_esm()` in Bootstrap.
- `elgg.echo()` → `i18n.echo()` (imported from `elgg/i18n`).
- `elgg.security.addToken()` → `security.addToken()` (imported from `elgg/security`).
- Deprecated `.live()` jQuery calls replaced with `$(document).on()`.
- Docker stack: Elgg ~5.1.x → ^6.0, PHPUnit ~9.5 → ~10.5.

## Migration notes (4.x → 5.x)

- Hook system merged into events: `elgg_register_plugin_hook_handler()` → `elgg_register_event_handler()`
- Handler classes: `use Elgg\Hook` → `use Elgg\Event`; parameter type `Hook $hook` → `Event $event`
- `languages/en.php`: `add_translation()` removed; language files must return the array directly
- `ElggEntity::delete()` is `void` in 5.x (was `bool`); test assertions adapted accordingly
- `get_entity()` returns `null` (not `false`) for missing entities in 5.x
- `elgg_get_session()->setLoggedInUser()` moved to `_elgg_services()->session_manager->setLoggedInUser()`
- Docker stack: PHP 7.4→8.2, MySQL 5.7→8.0, Elgg ~5.1.x
