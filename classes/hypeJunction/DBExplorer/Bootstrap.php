<?php

namespace hypeJunction\DBExplorer;

use Elgg\DefaultPluginBootstrap;

/**
 * Plugin bootstrap.
 */
class Bootstrap extends DefaultPluginBootstrap {

	/**
	 * {@inheritdoc}
	 */
	public function init(): void {
		$actions_path = $this->plugin->getPath() . 'actions/db_explorer/';

		elgg_register_action('db_explorer/entities', $actions_path . 'entities.php', 'admin');
		elgg_register_action('db_explorer/owned_entities', $actions_path . 'owned_entities.php', 'admin');
		elgg_register_action('db_explorer/contained_entities', $actions_path . 'contained_entities.php', 'admin');
		elgg_register_action('db_explorer/river_items', $actions_path . 'river_items.php', 'admin');
		elgg_register_action('db_explorer/metadata', $actions_path . 'metadata.php', 'admin');
		elgg_register_action('db_explorer/metadata_ownership', $actions_path . 'metadata_ownership.php', 'admin');
		elgg_register_action('db_explorer/annotations', $actions_path . 'annotations.php', 'admin');
		elgg_register_action('db_explorer/annotations_ownership', $actions_path . 'annotations_ownership.php', 'admin');
		elgg_register_action('db_explorer/private_settings', $actions_path . 'private_settings.php', 'admin');
		elgg_register_action('db_explorer/access_collections_ownership', $actions_path . 'access_collections_ownership.php', 'admin');
		elgg_register_action('db_explorer/access_collections_membership', $actions_path . 'access_collections_membership.php', 'admin');
		elgg_register_action('db_explorer/entity_relationships', $actions_path . 'entity_relationships.php', 'admin');
		elgg_register_action('db_explorer/batch', $actions_path . 'batch.php');
		elgg_register_action('db_explorer/user/validate', $actions_path . 'user/validate.php');
		elgg_register_action('db_explorer/user/ban', $actions_path . 'user/ban.php');
		elgg_register_action('db_explorer/user/unban', $actions_path . 'user/unban.php');
		elgg_register_action('db_explorer/user/enable', $actions_path . 'user/enable.php');
		elgg_register_action('db_explorer/user/disable', $actions_path . 'user/disable.php');
		elgg_register_action('db_explorer/user/delete', $actions_path . 'user/delete.php');
		elgg_register_action('db_explorer/content/enable', $actions_path . 'content/enable.php');
		elgg_register_action('db_explorer/content/disable', $actions_path . 'content/disable.php');
		elgg_register_action('db_explorer/content/delete', $actions_path . 'content/delete.php');

		// jqgrid + its locale are vendored classic jQuery plugins. Register them by
		// absolute URL — the previous elgg_get_simplecache_url('framework/db_explorer/
		// jqgrid*.js') pointed at views that do not exist, so the importmap entries
		// never resolved and `import 'jqgrid'` aborted the DB-explorer module.
		$locale = elgg_get_current_language();
		$locale_file = "vendors/jqgrid/js/i18n/grid.locale-{$locale}.js";
		if (!file_exists($this->plugin->getPath() . $locale_file)) {
			$locale_file = 'vendors/jqgrid/js/i18n/grid.locale-en.js';
		}
		elgg_register_esm('jqgrid.locale', elgg_normalize_url("mod/hypedbexplorer/{$locale_file}"));
		elgg_register_esm('jqgrid', elgg_normalize_url('mod/hypedbexplorer/vendors/jqgrid/js/minified/jquery.jqGrid.min.js'));

		elgg_register_external_file('css', 'db_explorer.jquery-ui', elgg_get_simplecache_url('framework/db_explorer/jquery-ui.css'));
		elgg_register_external_file('css', 'db_explorer.stylesheet', elgg_get_simplecache_url('framework/db_explorer/stylesheet.css'));

		if (elgg_is_admin_logged_in()) {
			if (elgg_get_config('debug')) {
				elgg_register_event_handler('register', 'menu:user_hover', UserHoverMenuSetup::class);
				elgg_register_event_handler('register', 'menu:entity', EntityMenuSetup::class);
			}

			elgg_register_menu_item('page', [
				'name' => 'db_explorer',
				'href' => 'admin/developers/db_explorer',
				'text' => elgg_echo('admin:developers:db_explorer'),
				'context' => 'admin',
				'section' => 'develop',
			]);
		}
	}
}
