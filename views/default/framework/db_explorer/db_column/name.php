<?php

$data = elgg_extract('data', $vars);
$name = $data->name;

echo elgg_view('output/text', array(
	'value' => $name
));