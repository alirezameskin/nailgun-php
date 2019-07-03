<?php

namespace Nailgun\Protocol;

class Header
{
    const CHUNK_HEADER_LENGTH = 5;

    const ARGUMENT    = 'A';
    const COMMAND     = 'C';
    const CURRENT_DIR = 'D';
    const ENVIRONMENT = 'E';
    const EXIT        = 'X';
    const HEARTBEAT   = 'H';
    const LONG_ARG    = 'L';
    const SEND_INPUT  = 'S';
    const STDIN       = '0';
    const STD_OUT     = '1';
    const STD_ERR     = '2';
    const STD_IN_EOF  = '.';

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

        return new static(chr($extract['type']), $extract['size']);
    }
}
