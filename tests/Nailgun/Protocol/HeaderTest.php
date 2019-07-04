<?php

namespace Nailgun\Tests\Protocol;

use Nailgun\Protocol\Header;
use PHPStan\Testing\TestCase;

class HeaderTest extends TestCase
{
    /**
     * @group protocol
     */
    public function testEncode()
    {
        $code = pack("Nc", 10, ord('A'));
        $header = new Header('A', 10);
        $this->assertEquals($code, $header->encode());
    }

    /**
     * @group protocol
     */
    public function testDecode()
    {
        $code = pack("Nc", 10, ord('A'));
        $header = Header::decode($code);

        $this->assertInstanceOf(Header::class, $header);
        $this->assertEquals('A', $header->getType());
        $this->assertEquals(10, $header->getLength());
    }

    /**
     * @group protocol
     */
    public function testGetType()
    {
        $header = new Header(Header::ARGUMENT, 10);
        $this->assertEquals(Header::ARGUMENT, $header->getType());
    }

    /**
     * @group protocol
     */
    public function testGetLength()
    {
        $header = new Header(Header::ARGUMENT, 10);
        $this->assertEquals(10, $header->getLength());
    }
}
