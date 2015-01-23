<?php

namespace hypeJunction\DBExplorer;

$user_action = get_input('user_action', false);
$content_action = get_input('content_action', false);

$ha = access_get_show_hidden_status();
access_show_hidden_entities(true);

if (elgg_action_exists($content_action)) {
	action($content_action, false);
}

if (elgg_action_exists($user_action)) {
	action($user_action, false);
}

access_show_hidden_entities($ha);

forward(REFERER);
