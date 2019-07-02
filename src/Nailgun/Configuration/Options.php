<?php

namespace Nailgun\Configuration;

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
    }

    /**
     * @return string[]
     */
    public function getEnvironments(): array
    {
        return $this->environments;
    }

    /**
     * @return string
     */
    public function getCurrentDirectory(): string
    {
        return $this->currentDirectory;
    }
}
