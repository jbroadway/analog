<?php

namespace Analog\Handler;

/**
 * Echo output directly to the console.
 *
 * Usage:
 *
 *     Analog::handler (Analog\Handler\Echo::init ());
 *     
 *     Analog::log ('Log me');
 *
 * Note: Uses Analog::$format for the output format.
 */
class Echo {
	public static function init () {
		return function ($info) {
			vprintf (\Analog\Analog::$format, $info);
		};
	}
}
