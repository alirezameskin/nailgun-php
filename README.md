# nailgun-php

A php Nailgun client API.

The Nailgun client API lets you run java code in Nailgun directly from PHP.


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
    'output' => new \Nailgun\Connection\Stream(fopen("php://stdout", "w")),
    'error'  => new \Nailgun\Connection\Stream(fopen("php://stderr", "w")),
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
    'output'       => new \Nailgun\Connection\Stream(fopen("file:///tmp/output", "a+")),
    'error'        => new \Nailgun\Connection\Stream(fopen("file:///tmp/error", "a+")),
    'directory'    => '/home/alireza/projects/',
    'environments' => [
        'HOME' => '/home/alireza'
    ],
];
$result = $client->run("com.facebook.nailgun.examples.HelloWorld", $options);
$client->disconnect();

echo $result->getExitCode();

```
