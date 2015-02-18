<?php

namespace Analog\Handler;

/**
 * Send the log message to the specified collection in a
 * MongoDB database.
 *
 * Usage:
 *
 *     Analog::handler (Analog\Handler\Mongo::init (
 *         'localhost:27017', // connection string
 *         'mydb',            // database name
 *         'log'              // collection name
 *     ));
 *
 * Alternately, if you have an existing Mongo connection,
 * you can simply initialize it with that:
 *
 *     $conn = new MongoClient ('localhost:27017');
 *     Analog::handler (Analog\Handler\Mongo::init (
 *         $conn,  // Mongo object
 *         'mydb', // database name
 *         'log'   // collection name
 *     ));
 */
class Mongo {
	public static function init ($server, $database, $collection) {
		if ($server instanceof \MongoClient) {
			$db = $server->{$database};
		} else {
			$conn = new \MongoClient ("mongodb://$server");
			$db = $conn->{$database};
		}
		return function ($info) use ($db, $collection) {
			$db->{$collection}->insert ($info);
		};
	}
}
