<?php

namespace Nailgun\Tests\Configuration;

use Nailgun\Configuration\Options;
use PHPStan\Testing\TestCase;

class OptionsTest extends TestCase
{
    /**
     * @group configuration
     */
    public function testGetEnvironmentsEmpty()
    {
        $options = new Options([]);
        $environments = $options->getEnvironments();

        $this->assertArrayHasKey('NAILGUN_FILESEPARATOR', $environments);
        $this->assertArrayHasKey('NAILGUN_PATHSEPARATOR', $environments);
        $this->assertEquals(DIRECTORY_SEPARATOR, $environments['NAILGUN_FILESEPARATOR']);
        $this->assertEquals(PATH_SEPARATOR, $environments['NAILGUN_PATHSEPARATOR']);
    }

    /**
     * @group configuration
     */
    public function testGetEnvironments()
    {
        $options = new Options([
            'environments' => [
                "ENV1" => 'ENV_VALUE1',
                "ENV2" => 'ENV_VALUE2',
            ]
        ]);

        $environments = $options->getEnvironments();

        $this->assertArrayHasKey('ENV1', $environments);
        $this->assertArrayHasKey('ENV2', $environments);
        $this->assertArrayHasKey('NAILGUN_FILESEPARATOR', $environments);
        $this->assertArrayHasKey('NAILGUN_PATHSEPARATOR', $environments);

        $this->assertEquals(DIRECTORY_SEPARATOR, $environments['NAILGUN_FILESEPARATOR']);
        $this->assertEquals(PATH_SEPARATOR, $environments['NAILGUN_PATHSEPARATOR']);
        $this->assertEquals('ENV_VALUE1', $environments['ENV1']);
        $this->assertEquals('ENV_VALUE2', $environments['ENV2']);
    }

    /**
     * @group configuration
     */
    public function testGetCurrentDirectory()
    {
        $options   = new Options([]);
        $directory = getcwd();

        $this->assertEquals(
            $directory,
            $options->getCurrentDirectory()
        );
    }

    /**
     * @group configuration
     */
    public function testGetCurrentDirectoryWithProvidedValue()
    {
        $directory = '/usr/local/bin/temp';
        $options   = new Options(['directory' => $directory]);

        $this->assertEquals(
            $directory,
            $options->getCurrentDirectory()
        );
    }

    /**
     * @group configuration
     */
    public function testGetOutputStreamDefault()
    {
        $options = new Options([]);
        $stream  = $options->getOutputStream(false);

        $this->assertInternalType('resource', $options->getOutputStream());
    }

    /**
     * @group configuration
     */
    public function testGetErrorStreamDefault()
    {
        $options = new Options([]);
        $this->assertInternalType('resource', $options->getOutputStream());
    }

    /**
     * @group configuration
     */
    public function testGetOutputStream()
    {
        $stream  = fopen("php://memory", "rw");
        $options = new Options(['output' => $stream]);

        $this->assertEquals($stream, $options->getOutputStream());

        $this->expectException(\InvalidArgumentException::class);
        new Options(['output' => "invalid data"]);
    }

    /**
     * @group configuration
     */
    public function testGetErrorStream()
    {
        $stream  = fopen("php://memory", "rw");
        $options = new Options(['error' => $stream]);

        $this->assertEquals($stream, $options->getErrorStream());

        $this->expectException(\InvalidArgumentException::class);
        new Options(['error' => "invalid data"]);
    }
}
