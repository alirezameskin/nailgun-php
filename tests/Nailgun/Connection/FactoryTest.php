<?php

namespace Nailgun\Tests\Connection;

use Nailgun\Connection\ConnectionInterface;
use Nailgun\Connection\Factory;
use PHPStan\Testing\TestCase;

class FactoryTest extends TestCase
{
    /**
     * @group connection
     */
    public function testCreate()
    {
        $factory = new Factory();

        $connection = $factory->create("127.0.0.1", 2113, 30);
        $this->assertInstanceOf(ConnectionInterface::class, $connection);
    }
}
