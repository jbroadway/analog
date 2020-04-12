<?php

require '../lib/Analog.php';

// The log level (3rd value) must be formatted as a string
Analog::$format = "%s - %s - %s - %s\n";

Analog::handler (Analog\Handler\LevelName::init (
	Analog\Handler\EchoConsole::init ()
));

// These should output with warning levels converted to text
Analog::log ('Debugging...', Analog::DEBUG);
Analog::log ('Minor warning...', Analog::WARNING);
Analog::log ('An error...', Analog::ERROR);

?>