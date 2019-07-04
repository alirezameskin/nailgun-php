<?php

namespace Nailgun\Tests\Exception;

use Nailgun\Exception\ConnectionException;
use PHPStan\Testing\TestCase;

class ConnectionExceptionTest extends TestCase
{
    /**
     * @group disconnected
     */
    public function testExceptionMessage()
    {
        $message = 'This is a connection exception.';
        $this->expectException('Nailgun\Exception\ConnectionException');
        $this->expectExceptionMessage($message);

        throw new ConnectionException($message);
    }
    /**
     * @group disconnected
     */
    public function testExceptionClass()
    {
        $exception = new ConnectionException();
        $this->assertInstanceOf('Nailgun\Exception\ConnectionException', $exception);
    }
}
