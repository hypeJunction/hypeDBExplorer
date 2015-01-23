<?php

$entity = elgg_extract('entity', $vars);

if (!elgg_instanceof($entity)) {
	return;
}

if (!elgg_instanceof($entity, 'user')) {
	return;
}

$setter = elgg_extract('setter', $vars);
$note = elgg_extract('note', $vars);

$head = elgg_echo('db_explorer:unban:email:head', array(
	$setter->name
));

if ($note) {
	$body = elgg_echo('db_explorer:unban:email:note');
	$body .= "<blockquote>" . $note . "</blockquote>";
}

$footer = elgg_echo('db_explorer:unban:email:footer', array(
	$setter->name, elgg_normalize_url("messages/compose?send_to=$setter->guid")
));

echo elgg_view('output/longtext', array(
	'value' => elgg_view_module('message', $head, $body, array('footer' => $footer))
));
