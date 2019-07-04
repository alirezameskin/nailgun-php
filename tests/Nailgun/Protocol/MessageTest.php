<?php

namespace Nailgun\Tests\Protocol;

use Nailgun\Protocol\Header;
use Nailgun\Protocol\Message;
use PHPStan\Testing\TestCase;

class MessageTest extends TestCase
{
    /**
     * @group protocol
     */
    public function testEncode()
    {
        $argument = "TestArgument";
        $header   = new Header('A', strlen($argument));
        $message  = new Message($header, $argument);
        $encoded  = $message->encode();

        $this->assertEquals("AAAADEFUZXN0QXJndW1lbnQ=", base64_encode($encoded));
    }

    /**
     * @group protocol
     */
    public function testEnvironment()
    {
        $message = Message::environment("ENV_KEY", "ENV_VALUE");

        $this->assertEquals(Header::ENVIRONMENT, $message->getHeader()->getType());
        $this->assertEquals(strlen("ENV_KEY=ENV_VALUE"), $message->getHeader()->getLength());
        $this->assertEquals("ENV_KEY=ENV_VALUE", $message->getMessage());
    }

    /**
     * @group protocol
     */
    public function testGetMessage()
    {
        $argument = "TestMessage";
        $header   = new Header('0', strlen($argument));
        $message  = new Message($header, $argument);

        $this->assertEquals("TestMessage", $message->getMessage());
    }

    /**
     * @group protocol
     */
    public function testGetHeader()
    {
        $argument = "TestMessage";
        $header   = new Header('0', strlen($argument));
        $message  = new Message($header, $argument);

        $this->assertEquals($header, $message->getHeader());
    }

    /**
     * @group protocol
     */
    public function testDirectory()
    {
        $directory = "/usr/local/bin";
        $message = Message::directory($directory);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals($directory, $message->getMessage());
        $this->assertInstanceOf(Header::class, $message->getHeader());
        $this->assertEquals(Header::CURRENT_DIR, $message->getHeader()->getType());
        $this->assertEquals(strlen($directory), $message->getHeader()->getLength());
    }

    /**
     * @group protocol
     */
    public function testCommand()
    {
        $command = "com.test.HelloWorld";
        $message = Message::command($command);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals($command, $message->getMessage());
        $this->assertInstanceOf(Header::class, $message->getHeader());
        $this->assertEquals(Header::COMMAND, $message->getHeader()->getType());
        $this->assertEquals(strlen($command), $message->getHeader()->getLength());
    }

    /**
     * @group protocol
     */
    public function testBuild()
    {
        $content = "com.test.HelloWorld";
        $message = Message::build($content, Header::ARGUMENT);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals($content, $message->getMessage());
        $this->assertInstanceOf(Header::class, $message->getHeader());
        $this->assertEquals(Header::ARGUMENT, $message->getHeader()->getType());
        $this->assertEquals(strlen($content), $message->getHeader()->getLength());
    }
}
