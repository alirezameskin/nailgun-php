<?php

namespace Nailgun\Connection;

class Factory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(string $host, int $port, int $timeout): ConnectionInterface
    {
        return new SocketConnection($host, $port, $timeout);
    }
}
