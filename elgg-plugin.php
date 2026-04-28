<?php

return [

	'plugin' => [
		'id' => 'hypedbexplorer',
		'name' => 'hypeDBExplorer',
		'version' => '5.0.0',
		'description' => 'Database explorer tool for Elgg admins.',
		'author' => 'Ismayil Khayredinov',
		'category' => 'admin',
	],

	'bootstrap' => \hypeJunction\DBExplorer\Bootstrap::class,
];
