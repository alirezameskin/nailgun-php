<?php

namespace Nailgun\Protocol;

class Result
{
    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     */
    private $output;

    /**
     * @var string
     */
    private $error;

    /**
     * @param int    $code
     * @param string $output
     * @param string $error
     */
    public function __construct(int $code, string $output, string $error)
    {
        $this->code = $code;
        $this->output = $output;
        $this->error = $error;
    }

    /**
     * @return int
     */
    public function getExitCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }

    /**
     * @return string
     */
    public function getError(): string
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
