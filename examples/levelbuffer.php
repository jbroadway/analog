<?php

require '../lib/Analog.php';

Analog::handler (Analog\Handler\LevelBuffer::init (
	Analog\Handler\Mail::init (
		'you@example.com',
		'Attention: A critical error has occurred',
		'noreply@example.com'
	),
	Analog::CRITICAL
));

// none of these will trigger sending the log
Analog::log ('Debugging...', Analog::DEBUG);
Analog::log ('Minor warning...', Analog::WARNING);
Analog::log ('An error...', Analog::ERROR);

// but this will, and will include all the others in the log
Analog::log ('Oh noes!', Analog::URGENT);

?>