<?php

namespace hypeJunction\DBExplorer;

$user_action = get_input('user_action', false);
$content_action = get_input('content_action', false);

// Whitelist of dispatchable sub-actions (action name => relative file path).
// Elgg 5.x removed the procedural action() dispatcher, so the registered
// sub-action scripts are executed inline instead.
$dispatchable = [
	'db_explorer/user/validate' => 'user/validate.php',
	'db_explorer/user/ban' => 'user/ban.php',
	'db_explorer/user/unban' => 'user/unban.php',
	'db_explorer/user/enable' => 'user/enable.php',
	'db_explorer/user/disable' => 'user/disable.php',
	'db_explorer/user/delete' => 'user/delete.php',
	'db_explorer/content/enable' => 'content/enable.php',
	'db_explorer/content/disable' => 'content/disable.php',
	'db_explorer/content/delete' => 'content/delete.php',
];

$actions_path = \elgg_get_plugin_from_id('hypedbexplorer')->getPath() . 'actions/db_explorer/';

\elgg_call(ELGG_SHOW_DISABLED_ENTITIES, function () use ($content_action, $user_action, $dispatchable, $actions_path) {
	if ($content_action && isset($dispatchable[$content_action])) {
		require $actions_path . $dispatchable[$content_action];
	}

	if ($user_action && isset($dispatchable[$user_action])) {
		require $actions_path . $dispatchable[$user_action];
	}
});

return \elgg_redirect_response(REFERRER);
