<?php

elgg_register_action('db_explorer/entities', elgg_get_plugins_path() . 'hypeDBExplorer/actions/db_explorer/entities.php', 'admin');

elgg_register_action('db_explorer/users_entity', elgg_get_plugins_path() . 'hypeDBExplorer/actions/db_explorer/users_entity.php', 'admin');
elgg_register_action('db_explorer/objects_entity', elgg_get_plugins_path() . 'hypeDBExplorer/actions/db_explorer/objects_entity.php', 'admin');
elgg_register_action('db_explorer/groups_entity', elgg_get_plugins_path() . 'hypeDBExplorer/actions/db_explorer/groups_entity.php', 'admin');
elgg_register_action('db_explorer/sites_entity', elgg_get_plugins_path() . 'hypeDBExplorer/actions/db_explorer/sites_entity.php', 'admin');

elgg_register_action('db_explorer/metadata', elgg_get_plugins_path() . 'hypeDBExplorer/actions/db_explorer/metadata.php', 'admin');
elgg_register_action('db_explorer/metadata_ownership', elgg_get_plugins_path() . 'hypeDBExplorer/actions/db_explorer/metadata_ownership.php', 'admin');

elgg_register_action('db_explorer/annotations', elgg_get_plugins_path() . 'hypeDBExplorer/actions/db_explorer/annotations.php', 'admin');
elgg_register_action('db_explorer/annotations_ownership', elgg_get_plugins_path() . 'hypeDBExplorer/actions/db_explorer/annotations_ownership.php', 'admin');

elgg_register_action('db_explorer/private_settings', elgg_get_plugins_path() . 'hypeDBExplorer/actions/db_explorer/private_settings.php', 'admin');

elgg_register_action('db_explorer/access_collections_ownership', elgg_get_plugins_path() . 'hypeDBExplorer/actions/db_explorer/access_collections_ownership.php', 'admin');
elgg_register_action('db_explorer/access_collections_membership', elgg_get_plugins_path() . 'hypeDBExplorer/actions/db_explorer/access_collections_membership.php', 'admin');

elgg_register_action('db_explorer/entity_relationships', elgg_get_plugins_path() . 'hypeDBExplorer/actions/db_explorer/entity_relationships.php', 'admin');