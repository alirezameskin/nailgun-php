<?php

namespace Nailgun\Protocol;

class Header
{
    public const CHUNK_HEADER_LENGTH = 5;

    public const ARGUMENT    = 'A';
    public const COMMAND     = 'C';
    public const CURRENT_DIR = 'D';
    public const ENVIRONMENT = 'E';
    public const EXIT        = 'X';
    public const HEARTBEAT   = 'H';
    public const LONG_ARG    = 'L';
    public const SEND_INPUT  = 'S';
    public const STDIN       = '0';
    public const STD_OUT     = '1';
    public const STD_ERR     = '2';
    public const STD_IN_EOF  = '.';

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $length;

    /**
     * @param string $type
     * @param int    $length
     */
    public function __construct(string $type, int $length)
    {
        $this->type = $type;
        $this->length = $length;
    }

    /**
     * @return string
     */
    public function encode(): string
    {
        return pack("Nc", $this->length, ord($this->type));
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @param string $code
     *
     * @return Header
     */
    public static function decode(string $code): Header
    {
        $extract = unpack('Nsize/ctype', $code);

        return new static($extract['type'], $extract['size']);
    }
}
