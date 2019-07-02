<?php

namespace Nailgun\Connection;

use Nailgun\Protocol\Message;

class SocketConnection implements ConnectionInterface
{
    /**
     * @var resource
     */
    private $socket;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @param string $host
     * @param int    $port
     */
    public function __construct(string $host, int $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * {@inheritDoc}
     */
    public function connect()
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, 0);

        if (false === $socket) {
            throw new \Exception("Can not Connect to socket");
        }

        $this->socket = $socket;

        socket_connect($this->socket, $this->host, $this->port);
    }

    /**
     * {@inheritDoc}
     */
    public function disconnect()
    {
        socket_close($this->socket);
    }

    /**
     * {@inheritDoc}
     */
    public function write(Message $message)
    {
        $header = $message->getHeader();

        socket_write($this->socket, $header->encode());

        if ($header->getLength() > 0) {
            socket_write($this->socket, $message->getMessage());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function read(int $length): string
    {
        return (string) socket_read($this->socket, 5, MSG_WAITALL);
    }
}
