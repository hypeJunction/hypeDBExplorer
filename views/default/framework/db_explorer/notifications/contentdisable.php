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

$link = elgg_view('output/url', array(
	'text' => $title,
	'href' => $entity->getURL(),
	'target' => '_blank'
		));

$head = elgg_echo('db_explorer:content:disable:email:head', array(
	$setter->name, $link
));

if ($note) {
	$body = elgg_echo('db_explorer:content:disable:email:note');
	$body .= "<blockquote>" . $note . "</blockquote>";
}

$footer = elgg_echo('db_explorer:content:disable:email:footer', array(
	$setter->name, elgg_normalize_url("messages/compose?send_to=$setter->guid")
));

echo elgg_view('output/longtext', array(
	'value' => elgg_view_module('message', $head, $body, array('footer' => $footer))
));
