# nailgun-php

A php Nailgun client API.

The Nailgun client API lets you run java code in Nailgun directly from PHP.


## Install

```
composer require alireza/nailgun-php
```

## Example

```php
<?php

require "./vendor/autoload.php";

$client = new Nailgun\Client();
$client->connect('127.0.0.1', 2113);

$result = $client->run("com.facebook.nailgun.examples.HelloWorld");

if ($result->successful()) {
    print $result->getOutput();
} else {
    print $result->getError();
}
```
