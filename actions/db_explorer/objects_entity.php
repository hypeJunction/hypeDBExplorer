<?php
// The objects_entity table was removed in Elgg 3.x.
// Object-specific data is now stored as metadata on the entities table.

$results = [
	'page' => 1,
	'total' => 0,
	'records' => 0,
	'rows' => [],
];

header('Content-type: application/json; charset=UTF-8');
print json_encode($results);
exit;
