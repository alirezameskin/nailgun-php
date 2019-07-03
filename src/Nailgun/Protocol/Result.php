<?php

namespace Nailgun\Protocol;

use Psr\Http\Message\StreamInterface;

class Result
{
    /**
     * @var int
     */
    private $code;

    /**
     * @var StreamInterface
     */
    private $output;

    /**
     * @var StreamInterface
     */
    private $error;

    /**
     * @param int             $code
     * @param StreamInterface $output
     * @param StreamInterface $error
     */
    public function __construct(int $code, StreamInterface $output, StreamInterface $error)
    {
        $this->code   = $code;
        $this->output = $output;
        $this->error  = $error;
    }

    /**
     * @return int
     */
    public function getExitCode(): int
    {
        return $this->code;
    }

    /**
     * @return StreamInterface
     */
    public function getOutput(): StreamInterface
    {
        return $this->output;
    }

    /**
     * @return StreamInterface
     */
    public function getError(): StreamInterface
    {
        return $this->error;
    }

    /**
     * @return bool
     */
    public function successful(): bool
    {
        return 0 === $this->code;
    }
}
