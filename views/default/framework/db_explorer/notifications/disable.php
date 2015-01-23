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

$head = elgg_echo('db_explorer:disable:email:head', array(
	$setter->name
));

if ($note) {
	$body = elgg_echo('db_explorer:disable:email:note');
	$body .= "<blockquote>" . $note . "</blockquote>";
}

$footer = elgg_echo('db_explorer:disable:email:footer', array(
	elgg_get_site_entity()->email
));

echo elgg_view('output/longtext', array(
	'value' => elgg_view_module('message', $head, $body, array('footer' => $footer))
));
