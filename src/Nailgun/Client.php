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
     * @var bool
     */
    protected $connected = false;

    /**
     * @param FactoryInterface|null $factory
     */
    public function __construct(FactoryInterface $factory = null)
    {
        if (null === $factory) {
            $this->connectionFactory = new Factory();
        } else {
            $this->connectionFactory = $factory;
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
        $this->connected = true;
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
    public function run(string $command, $options = []): Result
    {
        if (!$this->connected) {
            throw new \RuntimeException("Firstly, connect method should be called");
        }

        $options = $this->createOptions($options);

        foreach ($options->getEnvironments() as $key => $value) {
            $this->connection->write(Message::environment($key, $value));
        }

        foreach ($options->getArguments() as $arg) {
            $this->connection->write(Message::argument($arg));
        }

        $this->connection->write(Message::directory($options->getCurrentDirectory()));
        $this->connection->write(Message::command($command));

        $input   = $options->getInputStream();
        $output  = $options->getOutputStream();
        $error   = $options->getErrorStream();

        if (null !== $input) {
            while (!feof($input)) {
                $content = fread($input, 2048);

                if (!empty($content)) {
                    $this->connection->write(Message::input($content));
                }
            }

            $this->connection->write(Message::endInput());
        }

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
