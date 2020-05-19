<?php

namespace Analog\Handler;

/**
 * Trigger an IFTTT webhook on logged info.
 *
 * Usage:
 *
 *     $event_name = 'event_name';
 *     $secret_key = 'abc123';
 *     Analog::handler (Analog\Handler\IFTTT::init ($event_name, $secret_key));
 *
 * Note: Requires cURL.
 */
class IFTTT {
	public static function init ($event_name, $secret_key) {
		return LevelName::init (function ($info) use ($event_name, $secret_key) {
			if (! extension_loaded ('curl')) {
				throw new \LogicException ('CURL extension not loaded.');
			}
			
			$data = [
				'value1' => $info['machine'],
				'value2' => $info['level'],
				'value3' => $info['message']
			];
			
			$ch = curl_init ();
			curl_setopt ($ch, CURLOPT_URL, 'https://maker.ifttt.com/trigger/' . $event_name . '/with/key/' . $secret_key);
			curl_setopt ($ch, CURLOPT_MAXREDIRS, 3);
			curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_VERBOSE, 0);
			curl_setopt ($ch, CURLOPT_HEADER, 0);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, json_encode ($data));
			curl_setopt ($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
			curl_exec ($ch);
			curl_close ($ch);
		});
	}
}