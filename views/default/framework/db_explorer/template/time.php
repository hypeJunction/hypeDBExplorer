<?php

$value = elgg_extract('value', $vars);

echo elgg_view_friendly_time($value);
//echo date("j M, y", $value);
//echo date("@ H:i:s", $value);