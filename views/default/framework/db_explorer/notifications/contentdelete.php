<?php

$entity = elgg_extract('entity', $vars);

if (!elgg_instanceof($entity)) {
	return;
}

$setter = elgg_extract('setter', $vars);
$note = elgg_extract('note', $vars);

$title = $entity->getDisplayName();
if (!$title) {
	$title = elgg_echo('untitled');
}

$head = elgg_echo('db_explorer:content:delete:email:head', array(
	$setter->name, $title
));

if ($note) {
	$body = elgg_echo('db_explorer:content:delete:email:note');
	$body .= "<blockquote>" . $note . "</blockquote>";
}

$footer = elgg_echo('db_explorer:content:delete:email:footer', array(
	$setter->name, elgg_normalize_url("messages/compose?send_to=$setter->guid")
));

echo elgg_view('output/longtext', array(
	'value' => elgg_view_module('message', $head, $body, array('footer' => $footer))
));
