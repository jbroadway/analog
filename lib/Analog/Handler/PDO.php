<?php

namespace Analog\Handler;

/**
 * Send the log message to the specified table in a database.
 *
 * Usage:
 *
 *     Analog::handler (Analog\Handler\PDO::init (
 *         $pdo,  // PDO connection object
 *         'logs' // database table name
 *     ));
 *
 * Alternately, you can pass the connection info as an array and it
 * will initialize a new PDO connection for logging:
 *
 *     $conn = [
 *         'mysql:host=localhost;dbname=example',
 *         'username',
 *         'password'
 *     ];
 *
 *     Analog::handler (Analog\Handler\PDO::init (
 *         $conn, // connection info
 *         'logs' // database table name
 *     ));
 *
 * A convenience method exists for creating the database table:
 *
 *     Analog\Handler\PDO::createTable ($pdo, 'logs');
 *
 * The schema it creates looks like this:
 *
 *     CREATE TABLE `logs` (
 *         `machine` varchar(48),
 *         `date` datetime,
 *         `level` int,
 *         `message` text,
 *         index (`machine`)
 *         index (`date`),
 *         index (`level`)
 *     );
 *
 * Note: The table name property should *never* come from an insecure
 * source, as it is not escaped for SQL injection prevention. The other
 * fields are properly protected, however.
 */
class PDO {
	public static function init ($pdo, $table) {
		if (is_array ($pdo)) {
			$pdo = new \PDO ($pdo[0], $pdo[1], $pdo[2], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
		}
		
		$stmt = $pdo->prepare (
			'insert into `' . $table . '` (`machine`, `date`, `level`, `message`) values (:machine, :date, :level, :message)'
		);
		
		return function ($info) use ($stmt, $table) {
			$stmt->execute ($info);
		};
	}
	
	public static function createTable ($pdo, $table) {
		if (is_array ($pdo)) {
			$pdo = new \PDO ($pdo[0], $pdo[1], $pdo[2]);
		}
		
		$pdo->beginTransaction ();
		
		$pdo->prepare (
			'create table `' . $table . '` (`machine` varchar(48), `date` datetime, `level` int, `message` text)'
		)->execute ();
	
		$pdo->prepare (
			'create index `' . $table . '_message` on `' . $table . '` (`machine`)'
		)->execute ();
	
		$pdo->prepare (
			'create index `' . $table . '_date` on `' . $table . '` (`date`)'
		)->execute ();
	
		$pdo->prepare (
			'create index `' . $table . '_level` on `' . $table . '` (`level`)'
		)->execute ();
		
		$pdo->commit ();
	}
}
