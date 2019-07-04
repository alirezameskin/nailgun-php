<?php

namespace Nailgun;

use InvalidArgumentException;
use Nailgun\Configuration\Options;
use Nailgun\Configuration\OptionsInterface;
use Nailgun\Connection\Factory;
use Nailgun\Connection\ConnectionInterface;
use Nailgun\Connection\FactoryInterface;
use Nailgun\Protocol\Message;
use Nailgun\Protocol\Result;

class Client implements ClientInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var FactoryInterface
     */
    protected $connectionFactory;

    /**
     * @param FactoryInterface|null $factory
     */
    public function __construct(FactoryInterface $factory = null)
    {
        if (null === $factory) {
            $this->connectionFactory = new Factory();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function connect(string $host = '127.0.0.1', int $port = 2113, int $timeout = 30)
    {
        if (null === $this->connection) {
            $this->connection = $this->connectionFactory->create($host, $port, $timeout);
        }

        $this->connection->connect();
    }

    /**
     * {@inheritDoc}
     */
    public function disconnect()
    {
        $this->connection->disconnect();
    }

    /**
     * {@inheritDoc}
     */
    public function run(string $command, array $options = []): Result
    {
        $options = $this->createOptions($options);

        $this->connect();

        foreach ($options->getEnvironments() as $key => $value) {
            $this->connection->write(Message::environment($key, $value));
        }

        $this->connection->write(Message::directory($options->getCurrentDirectory()));
        $this->connection->write(Message::command($command));

        $output   = $options->getOutputStream();
        $error    = $options->getErrorStream();
        $exitCode = $this->connection->stream($output, $error);

        return new Result($exitCode, $output, $error);
    }

    /**
     * @param OptionsInterface|array $options
     *
     * @return OptionsInterface
     *
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    protected function createOptions($options): OptionsInterface
    {
        if ($options instanceof OptionsInterface) {
            return $options;
        } else if (is_array($options)) {
            return new Options($options);
        }

        throw new InvalidArgumentException('Invalid type for client options.');
    }
}
