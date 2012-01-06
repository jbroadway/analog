<?php

require_once ('Analog.php');

class AnalogTest extends PHPUnit_Framework_TestCase {
	public static $log = '';

	function test_analog () {
		// Test default formatting
		$this->assertEquals (
			sprintf ("%s - %s - %d - %s\n", 'localhost', gmdate ('Y-m-d H:i:s'), 3, 'Testing'),
			Analog::format_message ('Testing')
		);

		// Test changing the format string
		Analog::format ("%s [%s] %d %s\n");
		$this->assertEquals (
			sprintf ("%s [%s] %d %s\n", 'localhost', gmdate ('Y-m-d H:i:s'), 7, 'Test two'),
			Analog::format_message ('Test two', Analog::DEBUG)
		);

		// Test using a closure
		Analog::format (function ($machine, $level, $message) {
			return sprintf ("%s, %s, %d, %s\n", $machine, gmdate ('Y-m-d H:i:s'), $level, $message);
		});
		$this->assertEquals (
			sprintf ("%s, %s, %d, %s\n", 'localhost', gmdate ('Y-m-d H:i:s'), 7, 'Test two'),
			Analog::format_message ('Test two', Analog::DEBUG)
		);

		// Check on /tmp/log.txt
		Analog::log ('Foo');
		$this->assertEquals (
			sprintf ("%s, %s, %d, %s\n", 'localhost', gmdate ('Y-m-d H:i:s'), 3, 'Foo'),
			file_get_contents (Analog::location ())
		);
		unlink (Analog::location ());

		// Test logging using a closure
		Analog::location (function ($msg) {
			AnalogTest::$log .= $msg;
		});

		Analog::log ('Testing');
		$this->assertEquals (
			sprintf ("%s, %s, %d, %s\n", 'localhost', gmdate ('Y-m-d H:i:s'), 3, 'Testing'),
			self::$log
		);
	}
}

?>