<?php

$data = elgg_extract('data', $vars);

$type = $data->type;
$subtype_id = $data->subtype;

$subtype = is_numeric($subtype_id) ? get_subtype_from_id($subtype_id) : $subtype_id;

echo $subtype;