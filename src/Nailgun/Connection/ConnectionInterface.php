<?php

namespace Nailgun\Connection;

use Nailgun\Protocol\Message;
use Psr\Http\Message\StreamInterface;

interface ConnectionInterface
{
    /**
     * Opens the underlying connection and connects to the server.
     *
     * @return void
     */
    public function connect();

    /**
     * Closes the underlying connection and disconnects from the server.
     *
     * @return void
     */
    public function disconnect();

    /**
     * Sends message to the connection
     *
     * @param Message $message
     *
     * @return void
     */
    public function write(Message $message);

    /**
     * @param StreamInterface $stdout
     * @param StreamInterface $stderr
     *
     * @return int
     */
    public function stream(StreamInterface $stdout, StreamInterface $stderr): int;
}
