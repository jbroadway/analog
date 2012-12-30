<?php

require_once ('lib/Analog.php');

class AnalogTest extends PHPUnit_Framework_TestCase {
	public static $log = '';

	function test_default () {
		@unlink (Analog::handler ());

		// Check it wrote correctly to temp file
		Analog::log ('Foo');
		$this->assertStringMatchesFormat (
			"localhost - %d-%d-%d %d:%d:%d - 3 - Foo\n",
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
		$this->assertStringMatchesFormat (
			"localhost, %d-%d-%d %d:%d:%d, 3, Foo\n",
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
		$this->assertStringMatchesFormat (
			"localhost, %d-%d-%d %d:%d:%d, 3, Testing\n",
			self::$log
		);
	}
}

?>