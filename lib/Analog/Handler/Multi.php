<?php

namespace Analog\Handler;

/**
 * Sends messages to one or more of the other handlers based on its
 * log level.
 *
 * Usage:
 *
 *     Analog::handler (Analog\Handler\Multi::init (array (
 *         // anything error or worse goes to this
 *         Analog::ERROR   => Analog\Handler\Mail::init ($to, $subject, $from),
 *
 *         // Warnings are sent here
 *         Analog::WARNING => Analog\Handler\File::init ('logs/warnings.log'),
 *
 *         // Debug and info messages sent here
 *         Analog::DEBUG   => Analog\Handler\Null::init () // do nothing
 *     )));
 *     
 *     // will be ignored
 *     Analog::log ('Ignore me', Analog::DEBUG);
 *
 *     // will be written to logs/warnings.log
 *     Analog::log ('Log me', Analog::WARNING);
 *
 *     // will trigger an email notice
 *     Analog::log ('Uh oh...', Analog::ERROR);
 */
class Multi {
	public static function init ($handlers) {
		return function ($info) use ($handlers) {
			$level = is_numeric ($info['level']) ? $info['level'] : 3;
			while ($level <= 7) {
				if (isset ($handlers[$level])) {
					return $handlers[$level] ($info);
				}
				$level++;
			}
		};
	}
}