<?php

namespace Nailgun\Tests;

use Nailgun\Client;
use Nailgun\Configuration\OptionsInterface;
use Nailgun\Connection\ConnectionInterface;
use Nailgun\Connection\Factory;
use Nailgun\Connection\FactoryInterface;
use Nailgun\Protocol\Message;
use Nailgun\Protocol\Result;
use PHPStan\Testing\TestCase;
use Prophecy\Argument;

class ClientTest extends TestCase
{
    public function testConstruct()
    {
        $client = new Client();
        $this->assertAttributeInstanceOf(Factory::class, "connectionFactory", $client);
    }

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

    public function testRunWithoutConnection()
    {
        $this->expectException(\RuntimeException::class);

        $client = new Client();
        $client->run("helloword");
    }

    public function testRun()
    {
        $command = "helloworld";

        $connection = $this->prophesize(ConnectionInterface::class);
        $connection->connect()->shouldBeCalled();
        $connection->write(Message::environment("NAILGUN_FILESEPARATOR", DIRECTORY_SEPARATOR))->shouldBeCalled();
        $connection->write(Message::environment("NAILGUN_PATHSEPARATOR", PATH_SEPARATOR))->shouldBeCalled();
        $connection->write(Message::directory(getcwd()))->shouldBeCalled();
        $connection->write(Message::command($command))->shouldBeCalled();
        $connection->stream(Argument::type("resource"), Argument::type("resource"))->willReturn(0)->shouldBeCalled();

        $factory = $this->prophesize(FactoryInterface::class);
        $factory
            ->create('192.168.0.1', 2114, 40)
            ->willReturn($connection->reveal())
            ->shouldBeCalled();

        $client = new Client($factory->reveal());
        $client->connect('192.168.0.1', 2114, 40);
        $result = $client->run($command);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(0, $result->getExitCode());
        $this->assertInternalType("resource", $result->getOutput(false));
        $this->assertInternalType("resource", $result->getError(false));
    }

    public function testRunWithArgument()
    {
        $command = "helloworld";

        $input = fopen("php://temp", "r+");
        fwrite($input, "Input Value");
        rewind($input);
        fseek($input, 0);

        $connection = $this->prophesize(ConnectionInterface::class);
        $connection->connect()->shouldBeCalled();
        $connection->write(Message::environment("NAILGUN_FILESEPARATOR", DIRECTORY_SEPARATOR))->shouldBeCalled();
        $connection->write(Message::environment("NAILGUN_PATHSEPARATOR", PATH_SEPARATOR))->shouldBeCalled();
        $connection->write(Message::environment("env1", "value1"))->shouldBeCalled();
        $connection->write(Message::environment("env2", "value2"))->shouldBeCalled();
        $connection->write(Message::directory(getcwd()))->shouldBeCalled();
        $connection->write(Message::argument('arg1'))->shouldBeCalled();
        $connection->write(Message::argument('arg2'))->shouldBeCalled();
        $connection->write(Message::command($command))->shouldBeCalled();
        $connection->write(Message::input("Input Value"))->shouldBeCalled();
        $connection->write(Message::endInput())->shouldBeCalled();
        $connection->stream(Argument::type("resource"), Argument::type("resource"))->willReturn(0)->shouldBeCalled();

        $factory = $this->prophesize(FactoryInterface::class);
        $factory
            ->create('192.168.0.1', 2114, 40)
            ->willReturn($connection->reveal())
            ->shouldBeCalled();


        $options = [
            "input" => $input,
            "arguments" => ['arg1', 'arg2'],
            "environments" => [
                'env1' => 'value1',
                'env2' => 'value2',
            ]
        ];
        $client = new Client($factory->reveal());
        $client->connect('192.168.0.1', 2114, 40);
        $result = $client->run($command, $options);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(0, $result->getExitCode());
        $this->assertInternalType("resource", $result->getOutput(false));
        $this->assertInternalType("resource", $result->getError(false));
    }

    public function testRunWithOptions()
    {
        $command = "test";
        $input = fopen("php://temp", "r+");
        fwrite($input, "Input Value");
        rewind($input);

        $options = $this->prophesize(OptionsInterface::class);
        $options->getEnvironments()->willReturn(['env3' => 'value3'])->shouldBeCalled();
        $options->getArguments()->willReturn(['arg'])->shouldBeCalled();
        $options->getCurrentDirectory()->willReturn("/opt/")->shouldBeCalled();
        $options->getInputStream()->willReturn($input)->shouldBeCalled();
        $options->getOutputStream()->willReturn(fopen('php://temp', 'rw'))->shouldBeCalled();
        $options->getErrorStream()->willReturn(fopen('php://temp', 'rw'))->shouldBeCalled();

        $connection = $this->prophesize(ConnectionInterface::class);
        $connection->connect()->shouldBeCalled();
        $connection->write(Message::environment("env3", "value3"))->shouldBeCalled();
        $connection->write(Message::directory('/opt/'))->shouldBeCalled();
        $connection->write(Message::argument('arg'))->shouldBeCalled();
        $connection->write(Message::command($command))->shouldBeCalled();
        $connection->stream(Argument::type("resource"), Argument::type("resource"))->willReturn(0)->shouldBeCalled();
        $connection->write(Message::input("Input Value"))->shouldBeCalled();
        $connection->write(Message::endInput())->shouldBeCalled();

        $factory = $this->prophesize(FactoryInterface::class);
        $factory
            ->create('192.168.0.1', 2114, 40)
            ->willReturn($connection->reveal())
            ->shouldBeCalled();


        $client = new Client($factory->reveal());
        $client->connect('192.168.0.1', 2114, 40);
        $result = $client->run($command, $options->reveal());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(0, $result->getExitCode());
        $this->assertInternalType("resource", $result->getOutput(false));
        $this->assertInternalType("resource", $result->getError(false));
    }

}
