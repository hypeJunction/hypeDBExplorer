<?php

$value = elgg_extract('value', $vars);

echo date("j M, y", $value);
echo '<br />';
echo date("@ H:i:s", $value);