<?php

// In Elgg 3.x, metadata no longer has owner_guid.
// This action returns empty results.

$results = [
	'page' => 1,
	'total' => 0,
	'records' => 0,
	'rows' => [],
];

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-type: application/json; charset=UTF-8');
print json_encode($results);
exit;
