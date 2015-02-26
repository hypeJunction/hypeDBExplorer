<?php

/**
 * Elgg Database Explorer
 *
 * @package Elgg
 * @subpackage hypeJunction\DBExplorer
 *
 * @author Ismayil Khayredinov <ismayil.khayredinov@gmail.com>
 * @copyright Copyright (c) 2013-2015, Ismayil Khayredinov
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2
 */

namespace hypeJunction\DBExplorer;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/lib/deprecated.php';
require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/lib/hooks.php';

elgg_register_event_handler('init', 'system', __NAMESPACE__ . '\\init');

/**
 * Initialize the plugin
 * @return void
 */
function init() {

	// Register actions
	$actions_path = dirname(__FILE__) . "/actions/db_explorer/";

	elgg_register_action('db_explorer/entities', $actions_path . 'entities.php', 'admin');

	elgg_register_action('db_explorer/users_entity', $actions_path . 'users_entity.php', 'admin');
	elgg_register_action('db_explorer/objects_entity', $actions_path . 'objects_entity.php', 'admin');
	elgg_register_action('db_explorer/groups_entity', $actions_path . 'groups_entity.php', 'admin');
	elgg_register_action('db_explorer/sites_entity', $actions_path . 'sites_entity.php', 'admin');

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

	// Register javascripts
	elgg_define_js('jqgrid', array(
		'src' => '/mod/hypeDBExplorer/vendor/jqgrid/js/minified/jquery.jqGrid.min.js',
		'deps' => array('jquery', 'jqgrid.locale'),
	));

	$locale = get_current_language();
	elgg_define_js('jqgrid.locale', array(
		'src' => "/mod/hypeDBExplorer/vendor/jqgrid/js/i18n/grid.locale-$locale.js",
		'deps' => array('jquery')
	));

	// Register stylesheets
	//elgg_register_css('jquery.jqgrid', '/mod/hypeDBExplorer/vendor/jqgrid/css/ui.jqgrid.css');
	elgg_register_css('db_explorer.jquery-ui', '/mod/hypeDBExplorer/vendor/jquery-ui/themes/smoothness/jquery-ui.min.css');
	elgg_register_css('db_explorer.stylesheet', elgg_get_simplecache_url('css', 'framework/db_explorer/stylesheet.css'));


	if (elgg_is_admin_logged_in()) {

		// Register menu items to quickly navigate to the DB explorer for the given user/entity
		elgg_register_plugin_hook_handler('register', 'menu:user_hover', __NAMESPACE__ . '\\user_hover_menu_setup');
		elgg_register_plugin_hook_handler('register', 'menu:entity', __NAMESPACE__ . '\\entity_menu_setup');

		// Register ajax views
		elgg_register_ajax_view('admin/developers/db_explorer');

		// Add an admin menu item
		elgg_register_menu_item('page', array(
			'name' => 'db_explorer',
			'href' => 'admin/developers/db_explorer',
			'text' => elgg_echo('admin:developers:db_explorer'),
			'context' => 'admin',
			'section' => 'develop'
		));
	}
}
