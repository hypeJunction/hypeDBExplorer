<?php

namespace hypeJunction\DBExplorer;

$user_action = get_input('user_action', false);
$content_action = get_input('content_action', false);

// In Elgg 3.x, use elgg_call() with ELGG_SHOW_DISABLED_ENTITIES
elgg_call(ELGG_SHOW_DISABLED_ENTITIES, function() use ($content_action, $user_action) {
	if (elgg_action_exists($content_action)) {
		action($content_action, false);
	}

	if (elgg_action_exists($user_action)) {
		action($user_action, false);
	}
});

forward(REFERER);
