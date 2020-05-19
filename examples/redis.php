<?php

require '../lib/Analog.php';

// Note: requires 'pecl install redis'
$redis = new Redis ();
$redis->connect ('localhost', '6379');
$key = 'logs';

Analog::handler (Analog\Handler\Redis::init ($redis, $key));

Analog::log ('Error message');
Analog::log ('Debug info', Analog::DEBUG);

echo $redis->rpop ($key) . PHP_EOL;
echo $redis->rpop ($key) . PHP_EOL;

?>
