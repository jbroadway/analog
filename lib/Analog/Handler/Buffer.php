<?php

namespace Analog\Handler;

/**
 * Buffers messages to be sent as a batch to another handler at the end
 * of the request. Currently only works with the Mail handler.
 *
 * Usage:
 *
 *     Analog::handler (Analog\Handler\Buffer::init (
 *         Analog\Handler\Mail::init ($to, $subject, $from)
 *     ));
 *     
 *     // will all be buffered into one email
 *     Analog::log ('Message one', Analog::DEBUG);
 *     Analog::log ('Message two', Analog::WARNING);
 *     Analog::log ('Message three', Analog::ERROR);
 *
 * Note: Uses Analog::$format to format the messages as they're appended
 * to the buffer.
 */
class Buffer {

	/**
	 * Accepts another handler function to be used on close().
	 */
	public static function init ($handler) {
		return new Buffer ($handler);
	}

	/**
	 * For use as a class instance
	 */
	private $_handler;
	private $_buffer = '';
	
	public function __construct ($handler) {
		$this->_handler = $handler;
	}

	public function log ($info) {
		$this->_buffer .= vsprintf (\Analog\Analog::$format, $info);
	}

	public function __destruct () {
		call_user_func ($this->_handler, $this->_buffer, true);
	}
}
