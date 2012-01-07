<?php

require '../Analog.php';
require '../Analog/Handler/Variable.php';

$log = '';

Analog::handler (Analog\Handler\Variable::init ($log));

Analog::log ('foo');
Analog::log ('bar');

echo $log;

?>