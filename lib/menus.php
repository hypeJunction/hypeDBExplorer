<?php

if (elgg_is_admin_logged_in()) {

	elgg_register_menu_item('page', array(
		'name' => 'db_explorer',
		'href' => 'admin/developers/db_explorer',
		'text' => elgg_echo('admin:developers:db_explorer'),
		'context' => 'admin',
		'section' => 'develop'
	));

}