<?php

require 'autoload.php';

Analog::handler (Analog\Handler\Stderr::init ());

Analog::log ('Output to php://stderr');

?>