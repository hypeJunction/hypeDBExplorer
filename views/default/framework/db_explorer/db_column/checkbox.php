<?php

$data = elgg_extract('data', $vars);
$value = $data->guid;

if ($data->type == 'user') {
	echo elgg_view('input/checkbox', array(
		'name' => 'user_guids[]',
		'value' => $value,
		'class' => 'js-dbexplorer-checkbox',
	));
} else {
	echo elgg_view('input/checkbox', array(
		'name' => 'content_guids[]',
		'value' => $value,
		'class' => 'js-dbexplorer-checkbox',
	));
}