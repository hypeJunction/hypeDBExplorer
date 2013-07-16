<?php

/* hypeDBExplorer
 *
 * Elgg Database Explorer
 * @package hypeJunction
 * @subpackage hypeDBExploere
 *
 * @author Ismayil Khayredinov <ismayil.khayredinov@gmail.com>
 * @copyright Copyright (c) 2013, Ismayil Khayredinov
 */

define('HYPEDBEXPLORER_RELEASE', 1372771756);

elgg_register_event_handler('init', 'system', 'hj_db_explorer_init');

function hj_db_explorer_init() {

	$path = elgg_get_plugins_path() . 'hypeDBExplorer/';

	$libraries = array(
		'actions',
		'menus',
		'assets',
	);

	foreach ($libraries as $lib) {
		$libpath = "{$path}lib/{$lib}.php";
		if (file_exists($libpath)) {
			elgg_register_library("dbexplorer:library:$lib", $libpath);
			elgg_load_library("dbexplorer:library:$lib");
		}
	}

	if (elgg_is_admin_logged_in()) {
		elgg_register_ajax_view('admin/developers/db_explorer');
	}
}
