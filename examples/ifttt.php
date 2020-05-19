<?php

require '../lib/Analog.php';

$event_name = 'test_event'; // From the webhook you setup
$secret_key = 'secret_key'; // From your Maker settings

Analog::handler (Analog\Handler\IFTTT::init ($event_name, $secret_key));

Analog::log ('foo');

?>