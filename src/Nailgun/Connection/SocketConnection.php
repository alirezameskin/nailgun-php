<?php

namespace Nailgun\Connection;

use Nailgun\Exception\ConnectionException;
use Nailgun\Protocol\Message;
use Psr\Http\Message\StreamInterface;

class SocketConnection implements ConnectionInterface
{
    /**
     * @var resource
     */
    public $socket;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var int
     */
    private $timeout;

    /**
     * @param string $host
     * @param int    $port
     * @param int    $timeout
     */
    public function __construct(string $host, int $port, int $timeout)
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    /**
     * {@inheritDoc}
     */
    public function connect()
    {
        $socket = stream_socket_client(
            "tcp://" . $this->host . ':' . $this->port,
            $errno,
            $errstr,
            $this->timeout,
            STREAM_CLIENT_CONNECT
        );

        if (false === $socket) {
            throw new ConnectionException($errstr, $errno);
        }

        $this->socket = $socket;
    }

    /**
     * {@inheritDoc}
     */
    public function disconnect()
    {
        fclose($this->socket);
    }

    /**
     * {@inheritDoc}
     */
    public function write(Message $message)
    {
        $header = $message->getHeader();

        fwrite($this->socket, $header->encode());

        if ($header->getLength() > 0) {
            fwrite($this->socket, $message->getMessage());
        }
    }

    /**
     * @return StreamInterface
     */
    public function stream(): StreamInterface
    {
        $temp = fopen("php://temp", "w+");

        if (false === $temp) {
            throw new \RuntimeException("Can not create a temporary stream");
        }

        stream_copy_to_stream($this->socket, $temp);

        return new Stream($temp);
    }
}
