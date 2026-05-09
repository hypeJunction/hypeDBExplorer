<?php

/**
 * @param mixed $url
 * @return mixed
 */
function hj_db_explorer_get_guid_from_url($url) {
	elgg_deprecated_notice(__FUNCTION__ . ' is deprecated', '3.0');
	return \hypeJunction\DBExplorer\get_guid_from_url($url);
}
