<?php

namespace hypeJunction\DBExplorer;

/**
 * Sniffs the URL to identify the guid
 * 
 * @param string $url URL to sniff
 * @return int
 */
function get_guid_from_url($url) {
	$sniffer = new UFCOE\Elgg\Url();
	return $sniffer->getGuid($url);
}
