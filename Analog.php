<?php

/**
 * Analog - PHP 5.3+ logging class
 *
 * Copyright (c) 2012 Johnny Broadway
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * A short and simple logging class for based on the idea of using closures for
 * configurability and extensibility. Functions as a static class, but you can
 * completely control the formatting and writing of log messages through closures.
 *
 * By default, this class will write to a file named /tmp/log.txt using a format
 * "machine - date - level - message\n".
 *
 * I wrote this because I wanted something simple and small like KLogger, and
 * preferably not torn out of a wider framework if possible. After searching,
 * I wasn't happy with the single-purpose libraries I found. With KLogger for
 * example, I didn't want an object instance but rather a static class, and I
 * wanted more flexibility in the back-end.
 *
 * I also found that the ones that had really flexible back-ends supported a lot
 * that I could never personally foresee needing, and could be easier to extend
 * with new back-ends that may be needed over time. Closures seem a natural fit for
 * this kind of thing.
 *
 * What about Analog, the logfile analyzer? Well, since it hasn't been updated
 * since 2004, I think it's safe to call a single-file PHP logging class the
 * same thing without it being considered stepping on toes :)
 *
 * Usage:
 *
 *     <?php
 *     
 *     require_once ('Analog.php');
 *     
 *     // Default logging to /tmp/log.txt
 *     Analog::log ('Log this error', Analog::ERROR);
 *     
 *     // Create a custom object format
 *     Analog::format (function ($machine, $level, $message) {
 *       return (object) array (
 *             'machine' => $machine,
 *             'date'    => gmdate ('Y-m-d H:i:s'),
 *             'level'   => $level,
 *             'message' => $message
 *         );
 *     });
 *     
 *     // Log to a MongoDB log collection
 *     Analog::location (function ($message) {
 *         static $conn = null;
 *         if (! $conn) {
 *             $conn = new Mongo ('localhost:27017');
 *         }
 *         $conn->mydb->log->insert ($message);
 *     });
 *     
 *     // Log an error
 *     Analog::log ('The sky is falling!');
 *     
 *     // Log some debug info
 *     Analog::log ('Debugging info', Analog::DEBUG);
 *     
 *     ?>
 *
 * @package Analog
 * @author Johnny Broadway
 */
class Analog {
	/**
	 * List of severity levels.
	 */
	const URGENT   = 0; // It's an emergency
	const ALERT    = 1; // Immediate action required
	const CRITICAL = 2; // Critical conditions
	const ERROR    = 3; // An error occurred
	const WARNING  = 4; // Something unexpected happening
	const NOTICE   = 5; // Something worth noting
	const INFO     = 6; // Information, not an error
	const DEBUG    = 7; // Debugging messages

	/**
	 * The default format for log messages (machine, date, level, message).
	 */
	private static $format = "%s - %s - %d - %s\n";

	/**
	 * The location to save the log output. See Analog::location()
	 * for details on setting this.
	 */
	private static $location = '/tmp/log.txt';

	/**
	 * The name of the current machine, defaults to $_SERVER['SERVER_ADDR']
	 * on first call to format_message(), or 'localhost' if $_SERVER['SERVER_ADDR']
	 * is not set (e.g., during CLI use).
	 */
	public static $machine = null;

	/**
	 * Format getter/setter. Usage:
	 *
	 *     Analog::format ("%s, %s, %d, %s\n");
	 *
	 * Using a closure:
	 *
	 *     Analog::format (function ($machine, $level, $message) {
	 *         return sprintf ("%s [%d] %s\n", gmdate ('Y-m-d H:i:s'), $level, $message);
	 *     });
	 */
	public static function format ($format = false) {
		if ($format) {
			self::$format = $format;
		}
		return self::$format;
	}

	/**
	 * Location getter/setter. Usage:
	 *
	 *    Analog::location ('my_log.txt');
	 *
	 * Using a closure:
	 *
	 *     Analog::location (function ($msg) {
	 *         return error_log ($msg);
	 *     });
 	 */
	public static function location ($location = false) {
		if ($location) {
			self::$location = $location;
		}
		return self::$location;
	}

	/**
	 * Format the message.
	 */
	public static function format_message ($message, $level = 3) {
		$format = self::format ();

		if (self::$machine === null) {
			self::$machine = (isset ($_SERVER['SERVER_ADDR'])) ? $_SERVER['SERVER_ADDR'] : 'localhost';
		}

		if (is_object ($format) && get_class ($format) === 'Closure') {
		    return $format (self::$machine, $level, $message);
		}
		return sprintf ($format, self::$machine, gmdate ('Y-m-d H:i:s'), $level, $message);
	}

	/**
	 * Write a raw message to the log.
	 */
	public static function write ($message) {
		$location = self::location ();
		if (is_object ($location) && get_class ($location) === 'Closure') {
			return $location ($message);
		}

		$f = fopen ($location, 'a');
		if (! $f) {
			throw new LogicException ('Could not open file for writing');
		}

		if (! flock ($f, LOCK_EX | LOCK_NB)) {
			throw new RuntimeException ('Could not lock file');
		}

		fwrite ($f, $message);
		flock ($f, LOCK_UN);
		fclose ($f);
		return true;
	}

	/**
	 * This is the main function you will call to log messages.
	 * Defaults to severity level Analog::ERROR.
	 * Usage:
	 *
	 *     Analog::log ('Debug info', Analog::DEBUG);
	 */
	public static function log ($message, $level = 3) {
		return self::write (self::format_message ($message, $level));
	}
}

?>