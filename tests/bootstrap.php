<?php

$elggRoot = dirname(dirname(dirname(__DIR__)));

require_once $elggRoot . '/vendor/autoload.php';

$testClassesDir = $elggRoot . '/vendor/elgg/elgg/engine/tests/classes';
spl_autoload_register(function ($class) use ($testClassesDir) {
	$file = $testClassesDir . '/' . str_replace('\\', '/', $class) . '.php';
	if (file_exists($file)) {
		require_once $file;
	}
});

$pluginRoot = dirname(__DIR__);
spl_autoload_register(function ($class) use ($pluginRoot) {
	if (strncmp($class, 'hypeJunction\\', 13) !== 0) {
		return;
	}
	$file = $pluginRoot . '/classes/' . str_replace('\\', '/', $class) . '.php';
	if (file_exists($file)) {
		require_once $file;
	}
});

\Elgg\Application::loadCore();
