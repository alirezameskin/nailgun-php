<?php

namespace Nailgun\Connection;

class Factory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(string $host, int $port): ConnectionInterface
    {
        return new SocketConnection($host, $port);
    }
}
