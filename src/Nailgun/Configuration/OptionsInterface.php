<?php

namespace Nailgun\Configuration;

interface OptionsInterface
{
    /**
     * @return string[]
     */
    public function getEnvironments(): array;

    /**
     * @return string
     */
    public function getCurrentDirectory(): string;
}
