<?php

namespace Nailgun\Configuration;

use Nailgun\Connection\Stream;
use Psr\Http\Message\StreamInterface;

class Options implements OptionsInterface
{
    /**
     * @var array
     */
    private $environments;

    /**
     * @var string
     */
    private $currentDirectory;

    /**
     * @var StreamInterface
     */
    private $outputStream;

    /**
     * @var StreamInterface
     */
    private $errorStream;

    /**
     * @param array $options
     *
     * @throws \Exception
     */
    public function __construct(array $options)
    {
        $this->environments = [
            'NAILGUN_FILESEPARATOR' => DIRECTORY_SEPARATOR,
            'NAILGUN_PATHSEPARATOR' => PATH_SEPARATOR,
        ];

        if (isset($options['environments'])) {
            $this->environments = array_merge($this->environments, $options['environments']);
        }

        if (isset($options['directory'])) {
            $this->currentDirectory = $options['directory'];
        } else {
            $cwd = getcwd();

            if (false === $cwd) {
                throw new \Exception("TODO change");
            }

            $this->currentDirectory = $cwd;
        }

        if (isset($options['output'])) {
            if (!$options['output'] instanceof StreamInterface) {
                throw new \InvalidArgumentException("Output stream should be instance of " . StreamInterface::class);
            }

            $this->outputStream = $options['output'];
        } else {
            $this->outputStream = $this->createTempStream();
        }

        if (isset($options['error'])) {
            if (!$options['error'] instanceof StreamInterface) {
                throw new \InvalidArgumentException("Error stream should be instance of " . StreamInterface::class);
            }

            $this->errorStream = $options['error'];
        } else {
            $this->errorStream = $this->createTempStream();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getEnvironments(): array
    {
        return $this->environments;
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentDirectory(): string
    {
        return $this->currentDirectory;
    }

    /**
     * {@inheritDoc}
     */
    public function getOutputStream(): StreamInterface
    {
        return $this->outputStream;
    }

    /**
     *
     * {@inheritDoc}
     */
    public function getErrorStream(): StreamInterface
    {
        return $this->errorStream;
    }

    /**
     * @return StreamInterface
     */
    protected function createTempStream(): StreamInterface
    {
        $temp  = fopen("php://temp", "rw");

        if (false === $temp) {
            throw new \RuntimeException("Can not create a temporary stream (php://stream)");
        }

        return new Stream($temp);
    }
}
