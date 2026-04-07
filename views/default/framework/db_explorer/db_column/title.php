<?php

$data = elgg_extract('data', $vars);
$title = $data->title;

echo elgg_view('output/text', [
	'value' => $title
]);