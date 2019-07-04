<?php

namespace Nailgun\Tests;

use Nailgun\Client;
use Nailgun\Connection\ConnectionInterface;
use Nailgun\Connection\FactoryInterface;
use PHPStan\Testing\TestCase;

class ClientTest extends TestCase
{
    public function testConnect()
    {
        $connection = $this->prophesize(ConnectionInterface::class);
        $connection->connect()->shouldBeCalled();

        $factory = $this->prophesize(FactoryInterface::class);
        $factory
            ->create('192.168.0.1', 2114, 40)
            ->willReturn($connection->reveal())
            ->shouldBeCalled();

        $client = new Client($factory->reveal());
        $client->connect('192.168.0.1', 2114, 40);
    }

    public function testDisconnect()
    {
        $connection = $this->prophesize(ConnectionInterface::class);
        $connection->connect()->shouldBeCalled();
        $connection->disconnect()->shouldBeCalled();

        $factory = $this->prophesize(FactoryInterface::class);
        $factory
            ->create('192.168.0.1', 2114, 40)
            ->willReturn($connection->reveal())
            ->shouldBeCalled();

        $client = new Client($factory->reveal());
        $client->connect('192.168.0.1', 2114, 40);
        $client->disconnect();
    }
}
