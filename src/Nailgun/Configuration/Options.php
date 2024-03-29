<?php

namespace Nailgun\Configuration;

class Options implements OptionsInterface
{
    /**
     * @var array
     */
    private $environments = [];

    /**
     * @var string[]
     */
    private $arguments = [];

    /**
     * @var string
     */
    private $currentDirectory;

    /**
     * @var resource
     */
    private $outputStream;

    /**
     * @var resource
     */
    private $errorStream;

    /**
     * @var resource|null
     */
    private $inputStream;

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

        if (isset($options['input'])) {
            if (is_resource($options['input'])) {
                $this->inputStream = $options['input'];
            } else {
                $this->inputStream = fopen("php://temp", "rw");
                fwrite($this->inputStream, (string) $options['input']);
                fseek($this->inputStream, 0);
            }
        }

        if (isset($options['arguments'])) {
            if (!is_array($options['arguments'])) {
                $this->arguments = [$options['arguments']];
            } else {
                $this->arguments = $options['arguments'];
            }
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
            if (!is_resource($options['output'])) {
                throw new \InvalidArgumentException("Output stream should be a valid resource");
            }

            $this->outputStream = $options['output'];
        } else {
            $this->outputStream = $this->createTempResource();
        }

        if (isset($options['error'])) {
            if (!is_resource($options['error'])) {
                throw new \InvalidArgumentException("Error stream should be a valid resource");
            }

            $this->errorStream = $options['error'];
        } else {
            $this->errorStream = $this->createTempResource();
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
    public function getArguments(): array
    {
        return $this->arguments;
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
    public function getOutputStream()
    {
        return $this->outputStream;
    }

    /**
     *
     * {@inheritDoc}
     */
    public function getErrorStream()
    {
        return $this->errorStream;
    }

    /**
     * @return resource|null
     */
    public function getInputStream()
    {
        return $this->inputStream;
    }

    /**
     * @return resource
     */
    protected function createTempResource()
    {
        $temp  = fopen("php://memory", "rw");

        if (false === $temp) {
            throw new \RuntimeException("Can not create a temporary stream (php://stream)");
        }

        return $temp;
    }
}
