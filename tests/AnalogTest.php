<?php

require_once ('lib/Analog.php');

class AnalogTest extends PHPUnit_Framework_TestCase {
	public static $log = '';

	function test_default () {
		@unlink (Analog::handler ());

		// Check it wrote correctly to temp file
		Analog::log ('Foo');
		$this->assertEquals (
			sprintf ("%s - %s - %d - %s\n", 'localhost', gmdate ('Y-m-d H:i:s'), 3, 'Foo'),
			file_get_contents (Analog::handler ())
		);
		unlink (Analog::handler ());
	}

	/**
	 * @depends test_default
	 */
	function test_format () {
		// Test changing the format string and write again
		Analog::$format = "%s, %s, %d, %s\n";
		Analog::log ('Foo');
		$this->assertEquals (
			sprintf ("%s, %s, %d, %s\n", 'localhost', gmdate ('Y-m-d H:i:s'), 3, 'Foo'),
			file_get_contents (Analog::handler ())
		);
		unlink (Analog::handler ());
	}

	/**
	 * @depends test_format
	 */
	function test_handler () {
		// Test logging using a closure
		Analog::handler (function ($msg) {
			AnalogTest::$log .= vsprintf (Analog::$format, $msg);
		});

		Analog::log ('Testing');
		$this->assertEquals (
			sprintf ("%s, %s, %d, %s\n", 'localhost', gmdate ('Y-m-d H:i:s'), 3, 'Testing'),
			self::$log
		);
	}
}

?>