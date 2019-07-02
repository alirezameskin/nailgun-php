<?php

namespace Nailgun\Connection;

interface FactoryInterface
{
    /**
     * @param string $host
     * @param int    $port
     *
     * @return ConnectionInterface
     */
    public function create(string $host, int $port): ConnectionInterface;
}
