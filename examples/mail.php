<?php

require '../Analog.php';
require '../Analog/Handler/Mail.php';

Analog::handler (Analog\Handler\Mail::init (
	'you@example.com',
	'Log message',
	'noreply@example.com'
));

Analog::log ('Error message');

?>