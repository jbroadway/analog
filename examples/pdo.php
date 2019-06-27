<?php

require '../lib/Analog.php';

$pdo = new PDO ('sqlite:example.sqlite', '', '', [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
]);

$table = 'logs';

// Helper method for creating the database table
Analog\Handler\PDO::createTable ($pdo, $table);

// Initialize Analog with your PDO connection and table
Analog::handler (Analog\Handler\PDO::init ($pdo, $table));

// Log some messages
Analog::log ('Error message');
Analog::log ('Debug info', Analog::DEBUG);

// Fetch all to show it worked
foreach ($pdo->query ('select * from `' . $table . '`') as $row) {
	print_r ($row);
}

// Cleanup
$pdo = null;
unlink ('example.sqlite');
