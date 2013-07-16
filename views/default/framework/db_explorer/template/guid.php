<?php

$value = elgg_extract('value', $vars);

echo elgg_view('output/url', array(
	'text' => $value,
	'href' => "admin/developers/db_explorer?guid=$value",
	'data-href' => "ajax/view/admin/developers/db_explorer?guid=$value",
	'class' => 'dbexplorer-popup'
));