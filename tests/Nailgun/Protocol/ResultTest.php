<?php

namespace Nailgun\Tests\Protocol;

use Nailgun\Protocol\Result;
use PHPStan\Testing\TestCase;
use Psr\Http\Message\StreamInterface;

class ResultTest extends TestCase
{
    /**
     * @group protocol
     */
    public function testExitCode()
    {
        $result = new Result(
            1001,
            $this->prophesize(StreamInterface::class)->reveal(),
            $this->prophesize(StreamInterface::class)->reveal()
        );

        $this->assertEquals(1001, $result->getExitCode());
    }

    /**
     * @group protocol
     */
    public function testSuccessful()
    {
        $result = new Result(
            0,
            $this->prophesize(StreamInterface::class)->reveal(),
            $this->prophesize(StreamInterface::class)->reveal()
        );

        $this->assertTrue($result->successful());

        $result = new Result(
            10,
            $this->prophesize(StreamInterface::class)->reveal(),
            $this->prophesize(StreamInterface::class)->reveal()
        );

        $this->assertFalse($result->successful());
    }

    /**
     * @group protocol
     */
    public function testGetOutputAndError()
    {
        $error  = $this->prophesize(StreamInterface::class)->reveal();
        $output = $this->prophesize(StreamInterface::class)->reveal();
        $result = new Result(0, $output, $error);

        $this->assertEquals($output, $result->getOutput());
        $this->assertEquals($error, $result->getError());
    }
}
