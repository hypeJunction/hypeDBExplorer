<?php

$data = elgg_extract('data', $vars);
$value = $data->guid_one;

echo elgg_view('framework/db_explorer/template/guid', array(
	'value' => $value
));
