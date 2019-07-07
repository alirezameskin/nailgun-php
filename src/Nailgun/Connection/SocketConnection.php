<?php

namespace Nailgun\Connection;

use Nailgun\Exception\ConnectionException;
use Nailgun\Protocol\Header;
use Nailgun\Protocol\Message;

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
     * {@inheritDoc}
     */
    public function stream($stdout, $stderr): int
    {
        do {
            $buffer = $this->read(Header::CHUNK_HEADER_LENGTH);
            $header = Header::decode($buffer);

            if ($header->getLength() <= 0) {
                continue;
            }

            switch ($header->getType()) {
                case Header::STDERR:
                    fwrite($stderr, $this->read($header->getLength()));
                    break;

                case Header::STDOUT:
                    fwrite($stdout, $this->read($header->getLength()));
                    break;

                case Header::EXIT:
                    return (int) $this->read($header->getLength());
                    break;
            }

        } while (true);

        throw new \RuntimeException('Error while reading bytes from the server.');
    }

    /**
     * @param int $length
     *
     * @return string
     */
    private function read(int $length)
    {
        $bytesLeft = $length;
        $bulkData  = '';

        do {
            $chunk = fread($this->socket, $bytesLeft);

            if ($chunk === false || $chunk === '') {
                throw new \RuntimeException('Error while reading bytes from the server.');
            }

            $bulkData .= $chunk;
            $bytesLeft = $length - strlen($bulkData);
        } while ($bytesLeft > 0);

        return $bulkData;
    }
}
