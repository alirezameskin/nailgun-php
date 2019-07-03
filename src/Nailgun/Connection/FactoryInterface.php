<?php

namespace Nailgun\Connection;

interface FactoryInterface
{
    /**
     * @param string $host
     * @param int    $port
     * @param int    $timeout
     *
     * @return ConnectionInterface
     */
    public function create(string $host, int $port, int $timeout): ConnectionInterface;
}
