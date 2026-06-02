<?php

namespace hypeJunction\DBExplorer;

/**
 * Sniffs the URL to identify the guid
 *
 * @param string $url URL to sniff
 * @return int
 * @deprecated 3.1.0
 */
function get_guid_from_url($url) {
	return 0;
}

/**
 * Executes a raw read-only SQL query and returns the result rows as objects.
 *
 * Elgg 5.x removed the procedural get_data() helper. This plugin inspects the
 * raw database schema, so it issues raw SQL through the Doctrine DBAL connection
 * exposed by the Database service. Rows are cast to stdClass to preserve the
 * object access pattern (e.g. $row->id) used by the explorer views.
 *
 * @param string $sql Raw SQL SELECT statement
 * @return \stdClass[] Result rows as objects (empty array when no rows match)
 */
function query(string $sql): array {
	$connection = \elgg()->db->getConnection('read');
	$rows = $connection->executeQuery($sql)->fetchAllAssociative();

	return array_map(static function (array $row): \stdClass {
		return (object) $row;
	}, $rows);
}
