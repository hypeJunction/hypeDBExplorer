<?php

$entity = elgg_extract('entity', $vars);

if (!$entity instanceof \ElggUser) {
	return;
}

$setter = elgg_extract('setter', $vars);
$note = elgg_extract('note', $vars);

$head = elgg_echo('db_explorer:enable:email:head', [
	$setter->name,
]);

if ($note) {
	$body = elgg_echo('db_explorer:enable:email:note');
	$body .= "<blockquote>" . $note . "</blockquote>";
}

$footer = elgg_echo('db_explorer:enable:email:footer', [
	$setter->name, elgg_normalize_url("messages/compose?send_to=$setter->guid"),
]);

echo elgg_view('output/longtext', [
	'value' => elgg_view_module('message', $head, $body, ['footer' => $footer]),
]);
