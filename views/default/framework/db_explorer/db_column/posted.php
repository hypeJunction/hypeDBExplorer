<?php

$data = elgg_extract('data', $vars);
$value = $data->posted;

echo elgg_view('framework/db_explorer/template/time', array(
	'value' => $value
));