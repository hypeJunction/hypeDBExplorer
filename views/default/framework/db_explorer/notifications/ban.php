<?php

$entity = elgg_extract('entity', $vars);

if (!$entity instanceof \ElggUser) {
	return;
}

$setter = elgg_extract('setter', $vars);
$note = elgg_extract('note', $vars);

$head = elgg_echo('db_explorer:ban:email:head', [
	$setter->name,
]);

if ($note) {
	$body = elgg_echo('db_explorer:ban:email:note');
	$body .= '<blockquote>' . $note . '</blockquote>';
}

$footer = elgg_echo('db_explorer:ban:email:footer', [
	elgg_get_site_entity()->email,
]);

echo elgg_view('output/longtext', [
	'value' => elgg_view_module('message', $head, $body, ['footer' => $footer]),
]);
