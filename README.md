# Analog - Minimal PHP logging library

![GitHub Workflow Status](https://img.shields.io/github/workflow/status/jbroadway/analog/Continuous%20Integration)
![GitHub](https://img.shields.io/github/license/jbroadway/analog)
![Packagist Version](https://img.shields.io/packagist/v/analog/analog)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/analog/analog)
![Packagist Downloads](https://img.shields.io/packagist/dt/analog/analog)

A minimal PHP logging package based on the idea of using closures
for configurability and extensibility. It functions as a static class, but you can
completely control the writing of log messages through a closure function
(aka [anonymous functions](http://ca3.php.net/manual/en/functions.anonymous.php)),
or use the `Analog\Logger` wrapper that implements the
[PSR-3 specification](https://www.php-fig.org/psr/psr-3/).

## Installation

Install the latest version with:

```bash
$ composer require analog/analog
```

## Usage

### Basic Usage

```php
<?php

use Analog\Analog;
use Analog\Handler\FirePHP;

Analog::handler (FirePHP::init ());

Analog::log ('Take me to your browser');
```

### Usage with [PSR-3](https://www.php-fig.org/psr/psr-3/)

```php
<?php

use Analog\Logger;
use Analog\Handler\Variable;

$logger = new Logger;

$log = '';

$logger->handler (Variable::init ($log));

$logger->alert ('Things are really happening right now!');

var_dump ($log);
```

### Usage with a custom handler

```php
<?php

use Analog\Analog;

// Default logging to /tmp/analog.txt
Analog::log ('Log this error');

// Log to a MongoDB log collection
Analog::handler (function ($info) {
	static $conn = null;
	if (! $conn) {
		$conn = new Mongo ('localhost:27017');
	}
	$conn->mydb->log->insert ($info);
});

// Log an alert
Analog::log ('The sky is falling!', Analog::ALERT);

// Log some debug info
Analog::log ('Debugging info', Analog::DEBUG);
```

### Usage without composer

Analog uses a simple autoloader internally, so if you don't have access to [composer](https://getcomposer.org/) you can clone this repository and include it like this:

```php
<?php

require 'analog/lib/Analog.php';

Analog::handler (Analog\Handler\Stderr::init ());

Analog::log ('Output to php://stderr');
```

For more examples, see the [examples](https://github.com/jbroadway/analog/tree/master/examples) folder.

## Logging Options

By default, this class will write to a file named `sys_get_temp_dir() . '/analog.txt'`
using the format `"machine - date - level - message\n"`, making it usable with no
customization necessary.

Analog also comes with dozens of pre-written handlers in the Analog/Handlers folder,
with examples for each in the examples folder. These include:

* [Amon](https://github.com/jbroadway/analog/blob/master/examples/amon.php) - Send logs to the [Amon](http://amon.cx/) server monitoring tool
* [Apprise](https://github.com/jbroadway/analog/blob/master/examples/apprise.php) - Send notifications through the [apprise](https://github.com/caronc/apprise) command line tool
* [Buffer](https://github.com/jbroadway/analog/blob/master/examples/buffer.php) - Buffer messages to send all at once (works with File, Mail, Stderr, and Variable handlers)
* [ChromeLogger](https://github.com/jbroadway/analog/blob/master/examples/chromelogger.php) - Sends messages to [Chrome Logger](http://craig.is/writing/chrome-logger) browser plugin
* [EchoConsole](https://github.com/jbroadway/analog/blob/master/examples/echoconsole.php) - Echo output directly to the console
* [File](https://github.com/jbroadway/analog/blob/master/examples/file.php) - Append messages to a file
* [FirePHP](https://github.com/jbroadway/analog/blob/master/examples/firephp.php) - Send messages to [FirePHP](http://www.firephp.org/) browser plugin
* [GELF](https://github.com/jbroadway/analog/blob/master/examples/gelf.php) - Send message to the [Graylog2](http://www.graylog2.org/) log management server
* [IFTTT](https://github.com/jbroadway/analog/blob/master/examples/ifttt.php) - Trigger webhooks via the [IFTTT](https://ifttt.com/) service
* [Ignore](https://github.com/jbroadway/analog/blob/master/examples/ignore.php) - Do nothing
* [LevelBuffer](https://github.com/jbroadway/analog/blob/master/examples/levelbuffer.php) - Buffer messages and send only if sufficient error level reached
* [LevelName](https://github.com/jbroadway/analog/blob/master/examples/levelname.php) - Convert log level numbers to names in log output
* [Mail](https://github.com/jbroadway/analog/blob/master/examples/mail.php) - Send email notices
* [Mongo](https://github.com/jbroadway/analog/blob/master/examples/mongo.php) - Save to MongoDB collection
* [Multi](https://github.com/jbroadway/analog/blob/master/examples/multi.php) - Send different log levels to different handlers
* [PDO](https://github.com/jbroadway/analog/blob/master/examples/pdo.php) - Send messages to any PDO database connection (MySQL, SQLite, PostgreSQL, etc.)
* [Post](https://github.com/jbroadway/analog/blob/master/examples/post.php) - Send messages over HTTP POST to another machine
* [Redis](https://github.com/jbroadway/analog/blob/master/examples/redis.php) - Save messages to Redis key using RPUSH
* [Slackbot](https://github.com/jbroadway/analog/blob/master/examples/slackbot.php) - Post messages to Slack via Slackbot
* [Stderr](https://github.com/jbroadway/analog/blob/master/examples/stderr.php) - Send messages to STDERR
* [Syslog](https://github.com/jbroadway/analog/blob/master/examples/syslog.php) - Send messages to syslog
* [Threshold](https://github.com/jbroadway/analog/blob/master/examples/threshold.php) - Only writes log messages above a certain threshold
* [Variable](https://github.com/jbroadway/analog/blob/master/examples/variable.php) - Buffer messages to a variable reference
* [WPMail](https://github.com/jbroadway/analog/blob/master/examples/wpmail.php) - Send email notices using Wordpress `wp_mail()`

So while it's a micro class, it's highly extensible and very capable out of the box too.

## Rationale

I wrote this because I wanted something very small and simple like
[KLogger](https://github.com/katzgrau/KLogger), and preferably not torn out
of a wider framework if possible. After searching, I wasn't happy with the
single-purpose libraries I found. With KLogger for example, I didn't want an
object instance but rather a static class, and I wanted more flexibility in
the back-end.

I also found some that had the flexibility also had more complexity, for example
[Monolog](https://github.com/Seldaek/monolog) is dozens of source files (not incl. tests).
With closures, this seemed to be a good balance of small without sacrificing
flexibility.

> What about Analog, the logfile analyzer? Well, since it hasn't been updated
> since 2004, I think it's safe to call a single-file PHP logging class the
> same thing without it being considered stepping on toes :)
