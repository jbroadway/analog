<?php

require '../Analog.php';
require '../Analog/Handler/Syslog.php';

Analog::handler (Analog\Handler\Syslog::init ('analog', 'user'));

Analog::log ('Error message', Analog::WARNING);

?>