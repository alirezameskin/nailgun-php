<?php

namespace Nailgun\Connection;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    const READABLE_MODES = '/r|a\+|ab\+|w\+|wb\+|x\+|xb\+|c\+|cb\+/';
    const WRITABLE_MODES = '/a|w|r\+|rb\+|rw|x|c/';

    /**
     * @var resource
     */
    private $resource;

    /**
     * @var bool
     */
    private $seekable;

    /**
     * @var bool
     */
    private $readable;

    /**
     * @var bool
     */
    private $writable;

    /**
     * @var int|null
     */
    private $size;

    /**
     * @param resource $resource
     */
    public function __construct($resource)
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException('Stream must be a resource');
        }

        $this->resource = $resource;

        $meta = stream_get_meta_data($this->resource);

        $this->seekable = (bool) $meta['seekable'];
        $this->readable = (bool) preg_match(self::READABLE_MODES, $meta['mode']);
        $this->writable = (bool) preg_match(self::WRITABLE_MODES, $meta['mode']);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        try {
            $this->seek(0);
            return (string) stream_get_contents($this->resource);
        } catch (\Throwable $e) {
            trigger_error(sprintf('%s::__toString exception: %s', self::class, (string) $e), E_USER_ERROR);
            return '';
        }
    }

    /**
     * {@inheritDoc}
     */
    public function close()
    {
        if (isset($this->resource)) {
            if (is_resource($this->resource)) {
                fclose($this->resource);
            }
            $this->detach();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function detach()
    {
        if (!isset($this->resource)) {
            return null;
        }

        $result = $this->resource;
        unset($this->resource);

        $this->size     = null;
        $this->readable = false;
        $this->writable = false;
        $this->seekable = false;
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getSize()
    {
        if ($this->size !== null) {
            return $this->size;
        }

        if (!isset($this->resource)) {
            return null;
        }

        $stats = fstat($this->resource);

        if (isset($stats['size'])) {
            $this->size = $stats['size'];
            return $this->size;
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function tell()
    {
        if (!isset($this->resource)) {
            throw new \RuntimeException('Stream is detached');
        }

        $result = ftell($this->resource);

        if ($result === false) {
            throw new \RuntimeException('Unable to determine stream position');
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function eof()
    {
        if (!isset($this->resource)) {
            throw new \RuntimeException('Stream is detached');
        }

        return feof($this->resource);
    }

    /**
     * {@inheritDoc}
     */
    public function isSeekable()
    {
        return $this->seekable;
    }

    /**
     * {@inheritDoc}
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        $whence = (int) $whence;

        if (!isset($this->resource)) {
            throw new \RuntimeException('Stream is detached');
        }

        if (!$this->seekable) {
            throw new \RuntimeException('Stream is not seekable');
        }

        if (fseek($this->resource, $offset, $whence) === -1) {
            throw new \RuntimeException('Unable to seek to stream position '
                . $offset . ' with whence ' . var_export($whence, true));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * {@inheritDoc}
     */
    public function isWritable()
    {
        return $this->writable;
    }

    /**
     * @param string $string
     *
     * @return bool|int
     */
    public function write($string)
    {
        if (!isset($this->resource)) {
            throw new \RuntimeException('Stream is detached');
        }

        if (!$this->writable) {
            throw new \RuntimeException('Cannot write to a non-writable stream');
        }

        $this->size = null;

        $result = fwrite($this->resource, $string);

        if ($result === false) {
            throw new \RuntimeException('Unable to write to stream');
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function isReadable()
    {
        return $this->readable;
    }

    /**
     * {@inheritDoc}
     */
    public function read($length)
    {
        if (!isset($this->resource)) {
            throw new \RuntimeException('Stream is detached');
        }

        if (!$this->readable) {
            throw new \RuntimeException('Cannot read from non-readable stream');
        }

        if ($length < 0) {
            throw new \RuntimeException('Length parameter cannot be negative');
        }

        if (0 === $length) {
            return '';
        }

        $string = fread($this->resource, $length);

        if (false === $string) {
            throw new \RuntimeException('Unable to read from stream');
        }

        return $string;
    }

    /**
     * {@inheritDoc}
     */
    public function getContents()
    {
        if (!isset($this->resource)) {
            throw new \RuntimeException('Stream is detached');
        }

        $contents = stream_get_contents($this->resource);

        if ($contents === false) {
            throw new \RuntimeException('Unable to read stream contents');
        }

        return $contents;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadata($key = null)
    {
        if (!$key) {
            return stream_get_meta_data($this->resource);
        }

        $meta = stream_get_meta_data($this->resource);

        return isset($meta[$key]) ? $meta[$key] : null;
    }

    /**
     * {@inheritDoc}
     */
    public function __destruct()
    {
        $this->close();
    }
}
