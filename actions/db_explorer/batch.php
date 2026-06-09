<?php

namespace hypeJunction\DBExplorer;

$user_action = get_input('user_action', false);
$content_action = get_input('content_action', false);

// Resolve a registered action to its handler script so we can run it in-process.
// Elgg 7.x removed the global action() dispatcher, so include the action file directly.
$resolve_action_file = static function (string $action): ?string {
	if (empty($action) || !elgg_action_exists($action)) {
		return null;
	}

	$actions = _elgg_services()->actions->getAllActions();
	$file = $actions[$action]['file'] ?? null;

	return (is_string($file) && is_file($file)) ? $file : null;
};

elgg_call(ELGG_SHOW_DISABLED_ENTITIES, function() use ($content_action, $user_action, $resolve_action_file) {
	$content_file = $resolve_action_file((string) $content_action);
	if ($content_file) {
		require $content_file;
	}

	$user_file = $resolve_action_file((string) $user_action);
	if ($user_file) {
		require $user_file;
	}
});

return elgg_redirect_response(REFERRER);
