<?php

namespace Analog\Handler;

/**
 * Echo output directly to the console.
 *
 * Usage:
 *
 *     Analog::handler (Analog\Handler\EchoConsole::init ());
 *     
 *     Analog::log ('Log me');
 *
 * Note: Uses Analog::$format for the output format.
 */
class EchoConsole {
	public static function init () {
		return function ($info) {
			vprintf (\Analog\Analog::$format, $info);
		};
	}
}
