<?php

require '../lib/Analog.php';

Analog::handler (Analog\Handler\EchoConsole::init ());

Analog::log ('Error message', Analog::WARNING);

?>