<?php

namespace Nailgun\Tests\Protocol;

use Nailgun\Protocol\Result;
use PHPStan\Testing\TestCase;

class ResultTest extends TestCase
{
    /**
     * @group protocol
     */
    public function testExitCode()
    {
        $result = new Result(
            1001,
            fopen("php://memory", "rw"),
            fopen("php://memory", "rw")
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
            fopen("php://memory", "rw"),
            fopen("php://memory", "rw")
        );

        $this->assertTrue($result->successful());

        $result = new Result(
            10,
            fopen("php://memory", "rw"),
            fopen("php://memory", "rw")
        );

        $this->assertFalse($result->successful());
    }

    /**
     * @group protocol
     */
    public function testGetOutputAndError()
    {
        $output = fopen("php://memory", "rw");
        $error  = fopen("php://memory", "rw");
        $result = new Result(0, $output, $error);

        $this->assertEquals($output, $result->getOutput(false));
        $this->assertEquals($error, $result->getError(false));
    }

    /**
     * @group protocol
     */
    public function testGetOutputAndErrorAsString()
    {
        $output = fopen("php://temp", "rw");
        $error  = fopen("php://temp", "rw");

        fwrite($output, "Standard Output");
        fwrite($error, "Standard Error");
        rewind($output);
        rewind($error);

        $result = new Result(0, $output, $error);

        $this->assertEquals("Standard Output", $result->getOutput(true));
        $this->assertEquals("Standard Error", $result->getError(true));
    }
}
