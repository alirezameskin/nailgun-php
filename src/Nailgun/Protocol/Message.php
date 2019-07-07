<?php

namespace Nailgun\Protocol;

class Message
{
    /**
     * @var Header
     */
    private $header;

    /**
     * @var string
     */
    private $message;

    /**
     * @param Header $header
     * @param string $message
     */
    public function __construct(Header $header, string $message)
    {
        $this->header = $header;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function encode(): string
    {
        return $this->header->encode() . $this->message;
    }

    /**
     * @return Header
     */
    public function getHeader(): Header
    {
        return $this->header;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $content
     * @param string $type
     *
     * @return Message
     */
    public static function build(string $content, string $type): self
    {
        return new static(
            new Header($type, strlen($content)),
            $content
        );
    }

    /**
     * @param string $command
     *
     * @return Message
     */
    public static function command(string $command): self
    {
        return self::build($command, Header::COMMAND);
    }

    /**
     * @param string $directory
     *
     * @return Message
     */
    public static function directory(string $directory): self
    {
        return self::build($directory, Header::CURRENT_DIR);
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return Message
     */
    public static function environment(string $key, string $value): self
    {
        return self::build("$key=$value", Header::ENVIRONMENT);
    }

    /**
     * @param string $argument
     *
     * @return Message
     */
    public static function argument(string $argument): self
    {
        return self::build($argument, Header::ARGUMENT);
    }

    /**
     * @param string $content
     *
     * @return Message
     */
    public static function input(string $content): self
    {
        return self::build($content, Header::STDIN);
    }

    /**
     * @return Message
     */
    public static function endInput(): self
    {
        return self::build("", Header::STDIN_EOF);
    }
}
