<?php

$data = elgg_extract('data', $vars);
$value = $data->prev_last_login;

echo elgg_view('framework/db_explorer/template/time', array(
	'value' => $value
));