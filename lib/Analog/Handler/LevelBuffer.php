<?php

namespace Analog\Handler;

/**
 * Buffers messages to be sent as a batch to another handler only when a
 * message of a certain level threshold has been received. This means for
 * example that you can trigger a handler only if an error has occurred.
 * Currently only works with the Mail handler.
 *
 * Inspired by the Monolog FingersCrossedHandler.
 *
 * Usage:
 *
 *     Analog::handler (Analog\Handler\LevelBuffer::init (
 *         Analog\Handler\Mail::init ($to, $subject, $from),
 *         Analog::ERROR
 *     ));
 *     
 *     // will all be buffered until something ERROR or above is logged
 *     Analog::log ('Message one', Analog::DEBUG);
 *     Analog::log ('Message two', Analog::WARNING);
 *     Analog::log ('Message three', Analog::URGENT);
 *
 * Note: Uses Analog::$format to format the messages as they're appended
 * to the buffer.
 */
class LevelBuffer {

	/**
	 * Accepts another handler function to be used on close().
	 * $until_level defaults to CRITICAL.
	 */
	public static function init ($handler, $until_level = 2) {
		return new LevelBuffer ($handler, $until_level);
	}

	/**
	 * For use as a class instance
	 */
	private $_handler;
	private $_until_level = 2;
	private $_buffer = '';

	public function __construct ($handler, $until_level = 2) {
		$this->_handler = $handler;
		$this->_until_level = $until_level;
	}

	public function log ($info) {
		$this->_buffer .= vsprintf (\Analog\Analog::$format, $info);
		if ($info['level'] <= $this->_until_level) {
			// flush and reset the buffer
			call_user_func ($this->_handler, $this->_buffer, true);
			$this->_buffer = '';
		}
	}
}