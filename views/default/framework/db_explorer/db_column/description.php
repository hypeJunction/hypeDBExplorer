<?php

$data = elgg_extract('data', $vars);
$description = $data->description;

echo elgg_view('output/text', [
	'value' => $description
]);
