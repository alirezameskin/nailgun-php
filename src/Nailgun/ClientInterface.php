<?php

namespace Nailgun;

use Nailgun\Protocol\Result;

interface ClientInterface
{
    /**
     * Runs the command
     *
     * @param string $command
     * @param array  $options
     *
     * @return Result
     */
    public function run(string $command, array $options = []): Result;

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
}
