<?php

/**
 * Sniff the URL to identify the guid
 * 
 * @param string $url
 * @return int
 */
function hj_db_explorer_get_guid_from_url($url) {

	if (!class_exists('UFCOE\\Elgg\\Url')) {
		require dirname(dirname(__FILE__)) . '/classes/UFCOE/Elgg/Url.php';
	}

	$sniffer = new UFCOE\Elgg\Url();

	return $sniffer->getGuid($url);
}
