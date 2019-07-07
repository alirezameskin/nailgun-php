<?php

namespace Nailgun;

use Nailgun\Configuration\OptionsInterface;
use Nailgun\Protocol\Result;

interface ClientInterface
{
    /**
     * Runs the command
     *
     * @param string                 $command
     * @param array|OptionsInterface $options
     *
     * @return Result
     */
    public function run(string $command, $options = []): Result;

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
