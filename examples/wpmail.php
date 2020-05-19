<?php

require '../lib/Analog.php';

// Note: This example needs to be copied into a Wordpress installation to work
Analog::handler (Analog\Handler\WPMail::init (
	'you@example.com',
	'Log message',
	'noreply@example.com',
	'log-email-template.php'
));

Analog::log ('Error message');

?>