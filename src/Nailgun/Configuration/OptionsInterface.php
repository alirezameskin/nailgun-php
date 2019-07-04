<?php

namespace Nailgun\Configuration;

interface OptionsInterface
{
    /**
     * @return string[]
     */
    public function getEnvironments(): array;

    /**
     * @return string[]
     */
    public function getArguments(): array;

    /**
     * @return string
     */
    public function getCurrentDirectory(): string;

    /**
     * @return resource
     */
    public function getOutputStream();

    /**
     * @return resource
     */
    public function getErrorStream();
}
