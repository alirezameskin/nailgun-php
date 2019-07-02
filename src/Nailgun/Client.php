<?php

namespace Nailgun;

use InvalidArgumentException;
use Nailgun\Configuration\Options;
use Nailgun\Configuration\OptionsInterface;
use Nailgun\Connection\Factory;
use Nailgun\Connection\ConnectionInterface;
use Nailgun\Connection\FactoryInterface;
use Nailgun\Protocol\Header;
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
    public function connect(string $host = '127.0.0.1', int $port = 2113)
    {
        if (null === $this->connection) {
            $this->connection = $this->connectionFactory->create($host, $port);
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

        $result = $this->parseResult();

        return $result;
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

    /**
     * @return Result
     */
    private function parseResult(): Result
    {
        $output   = "";
        $error    = "";
        $exitCode = 0;

        while (true) {
            $buffer = $this->connection->read(Header::CHUNK_HEADER_LENGTH);
            $header = Header::decode($buffer);

            if ($header->getLength() <= 0) {
                continue;
            }

            switch ($header->getType()) {
                case Header::STD_ERR:
                    $error .= $this->connection->read($header->getLength());
                    break;

                case Header::STD_OUT:
                    $output .= $this->connection->read($header->getLength());
                    break;

                case Header::EXIT:
                    $exitCode = (int) $this->connection->read($header->getLength());

                    return new Result($exitCode, $output, $error);
                    break;
            }
        }

        return new Result($exitCode, $output, $error);
    }
}
