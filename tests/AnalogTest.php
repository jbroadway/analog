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
	function test_tz_and_dates () {
		// Test changing the date_format
		Analog::$date_format = 'r'; // RFC2822 format
		Analog::log ('Foo');
		$this->assertStringMatchesFormat (
			"localhost, %s, %d %s %d %d:%d:%d +0000, 3, Foo\n",
			file_get_contents (Analog::handler ())
		);
		unlink (Analog::handler ());

		// Test changing the timezone
		Analog::$timezone = 'GMT-6';
		Analog::log ('Foo');
		$this->assertStringMatchesFormat (
			"localhost, %s, %d %s %d %d:%d:%d -0600, 3, Foo\n",
			file_get_contents (Analog::handler ())
		);
		unlink (Analog::handler ());
		
		Analog::$date_format = 'Y-m-d H:i:s';
		Analog::$timezone = 'GMT';
	}

	/**
	 * @depends test_tz_and_dates
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

		self::$log = '';
	}
	
	/**
	 * @depends test_handler
	 */
	function test_level () {
		// Test default_level change
		Analog::$default_level = 1;
		Analog::log ('Testing');
		$this->assertStringMatchesFormat (
			"localhost, %d-%d-%d %d:%d:%d, 1, Testing\n",
			self::$log
		);
	}
}

?>