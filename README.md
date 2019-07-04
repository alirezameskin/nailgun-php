# nailgun-php

A php Nailgun client API.

The Nailgun client API lets you run java code in Nailgun directly from PHP.

![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/meskin/nailgun-php/dev-master.svg)
[![Build Status](https://travis-ci.com/alirezameskin/nailgun-php.svg?branch=master)](https://travis-ci.com/alirezameskin/nailgun-php)
[![codecov](https://codecov.io/gh/alirezameskin/nailgun-php/branch/master/graph/badge.svg)](https://codecov.io/gh/alirezameskin/nailgun-php)

## Install

```
composer require meskin/nailgun-php
```

## Examples

```php
<?php

require "./vendor/autoload.php";

$client = new Nailgun\Client();
$client->connect('127.0.0.1', 2113);

$result = $client->run("com.facebook.nailgun.examples.HelloWorld");
$client->disconnect();

print 'Exit Code : ' . $result->getExitCode() . PHP_EOL;

if ($result->successful()) {
    print $result->getOutput();
} else {
    print $result->getError();
}
```

```php
<?php

require "./vendor/autoload.php";

$client = new Nailgun\Client();
$client->connect('127.0.0.1', 2113);

$options = [
    'output' => fopen("php://stdout", "w"),
    'error'  => fopen("php://stderr", "w"),
];
$result = $client->run("com.facebook.nailgun.examples.ThreadTest", $options);
$client->disconnect();

print 'Exit Code : ' . $result->getExitCode() . PHP_EOL;

```

```php

<?php
require "./vendor/autoload.php";

$client = new Nailgun\Client();
$client->connect('127.0.0.1', 2113);

$options = [
    'output'       => fopen("file:///tmp/output", "a+"),
    'error'        => fopen("file:///tmp/error", "a+"),
    'directory'    => '/home/alireza/projects/',
    'environments' => [
        'HOME' => '/home/alireza'
    ],
];
$result = $client->run("com.facebook.nailgun.examples.HelloWorld", $options);
$client->disconnect();

echo $result->getExitCode();

```
