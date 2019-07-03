<?php

namespace Nailgun\Configuration;

use Psr\Http\Message\StreamInterface;

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

    /**
     * @return StreamInterface
     */
    public function getOutputStream(): StreamInterface;

    /**
     * @return StreamInterface
     */
    public function getErrorStream(): StreamInterface;
}
