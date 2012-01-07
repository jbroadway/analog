<?php

require '../Analog.php';
require '../Analog/Handler/Post.php';

$log = '';

Analog::handler (Analog\Handler\Post::init ('http://localhost:8080/'));

Analog::log ('foo');
Analog::log ('bar');

echo $log;

?>