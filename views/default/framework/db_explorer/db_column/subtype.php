<?php

$data = elgg_extract('data', $vars);

// In Elgg 3.x, subtypes are stored as strings directly
$subtype = $data->subtype;

echo $subtype;
