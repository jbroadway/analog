<?php

namespace Analog\Handler;

/**
 * Send the output to STDERR.
 *
 * Usage:
 *
 *     Analog::handler (Analog\Handler\Stderr::init ());
 *     
 *     Analog::log ('Log me');
 *
 * Note: Uses Analog::$format for the appending format.
 */
class Stderr {
	public static function init () {
		return function ($info) {
			file_put_contents ('php://stderr', vsprintf (\Analog\Analog::format (), $info));
		};
	}
}