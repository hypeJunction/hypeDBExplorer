<?php

/**
 * Elgg Database Explorer
 *
 * @package hypeJunction
 * @subpackage hypeDBExplorer
 *
 * @author Ismayil Khayredinov <ismayil.khayredinov@gmail.com>
 * @copyright Copyright (c) 2013, Ismayil Khayredinov
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2
 */

define('HYPEDBEXPLORER_RELEASE', 1372771756);

elgg_register_event_handler('init', 'system', 'hj_db_explorer_init');

/**
 * Initialize the plugin
 */
function hj_db_explorer_init() {

	// Register actions
	$actions_path = dirname(__FILE__) . "/actions/db_explorer/";

	elgg_register_action('db_explorer/entities', $actions_path . 'entities.php', 'admin');

	elgg_register_action('db_explorer/users_entity', $actions_path . 'users_entity.php', 'admin');
	elgg_register_action('db_explorer/objects_entity', $actions_path . 'objects_entity.php', 'admin');
	elgg_register_action('db_explorer/groups_entity', $actions_path . 'groups_entity.php', 'admin');
	elgg_register_action('db_explorer/sites_entity', $actions_path . 'sites_entity.php', 'admin');

	elgg_register_action('db_explorer/metadata', $actions_path . 'metadata.php', 'admin');
	elgg_register_action('db_explorer/metadata_ownership', $actions_path . 'metadata_ownership.php', 'admin');

	elgg_register_action('db_explorer/annotations', $actions_path . 'annotations.php', 'admin');
	elgg_register_action('db_explorer/annotations_ownership', $actions_path . 'annotations_ownership.php', 'admin');

	elgg_register_action('db_explorer/private_settings', $actions_path . 'private_settings.php', 'admin');

	elgg_register_action('db_explorer/access_collections_ownership', $actions_path . 'access_collections_ownership.php', 'admin');
	elgg_register_action('db_explorer/access_collections_membership', $actions_path . 'access_collections_membership.php', 'admin');

	elgg_register_action('db_explorer/entity_relationships', $actions_path . 'entity_relationships.php', 'admin');

	
	// Register javascripts
	elgg_register_js('jquery.jqgrid.js', '/mod/hypeDBExplorer/vendors/jqgrid/js/jquery.jqGrid.min.js');
	$locale = get_language();
	elgg_register_js('jquery.jqgrid.locale.js', "/mod/hypeDBExplorer/vendors/jqgrid/js/i18n/grid.locale-$locale.js");

	elgg_register_simplecache_view('js/framework/db_explorer/jqgrid');
	elgg_register_js('dbexplorer.jqgrid.js', elgg_get_simplecache_url('js', 'framework/db_explorer/jqgrid'));

	// Register stylesheets
	elgg_register_css('jquery.ui.css', '/mod/hypeDBExplorer/vendors/jquery.ui/css/jquery-ui.custom.css');
	elgg_register_css('jquery.jqgrid.css', '/mod/hypeDBExplorer/vendors/jqgrid/css/ui.jqgrid.css');

	elgg_register_simplecache_view('css/framework/db_explorer/jqgrid');
	elgg_register_css('dbexplorer.jqgrid.css', elgg_get_simplecache_url('css', 'framework/db_explorer/jqgrid'));

	
	if (elgg_is_admin_logged_in()) {

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
