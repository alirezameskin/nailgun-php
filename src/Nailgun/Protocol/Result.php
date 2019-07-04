<?php

namespace Nailgun\Protocol;

class Result
{
    /**
     * @var int
     */
    private $code;

    /**
     * @var resource
     */
    private $output;

    /**
     * @var resource
     */
    private $error;

    /**
     * @param int      $code
     * @param resource $output
     * @param resource $error
     */
    public function __construct(int $code, $output, $error)
    {
        $this->code   = $code;
        $this->output = $output;
        $this->error  = $error;

        fseek($this->output, 0);
        fseek($this->error, 0);
    }

    /**
     * @return int
     */
    public function getExitCode(): int
    {
        return $this->code;
    }

    /**
     * @param bool $asString
     *
     * @return resource|string
     */
    public function getOutput(bool $asString = true)
    {
        if ($asString) {
            return (string) stream_get_contents($this->output);
        }

        return $this->output;
    }

    /**
     * @param bool $asString
     *
     * @return resource|string
     */
    public function getError(bool $asString = true)
    {
        if ($asString) {
            return (string) stream_get_contents($this->error);
        }

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
