<?php

namespace PHPixie\HTTP\Messages\Stream;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

/**
 * PSR-7 String stream
 */
class StringStream implements StreamInterface
{
    /**
     * @var string
     */
    protected $string;

    /**
     * Constructor
     * @param string $string
     */
    public function __construct($string = '')
    {
        $this->string = $string;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return (string) $this->string;
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        $this->detach();
    }

    /**
     * @inheritdoc
     */
    public function detach()
    {
        $this->string = null;
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getSize()
    {
        if($this->string === null) {
            return null;
        }
        
        return mb_strlen($this->string, '8bit');
    }

    /**
     * @inheritdoc
     */
    public function tell()
    {
        $this->assertNotDetached();
        return $this->getSize();
    }

    /**
     * @inheritdoc
     */
    public function eof()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isSeekable()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isWritable()
    {
        if($this->string === null) {
            return false;
        }
        
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isReadable()
    {
        if($this->string === null) {
            return false;
        }
        
        return true;
    }

    /**
     * @inheritdoc
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        throw new RuntimeException("String streams are not seakable");
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * @inheritdoc
     */
    public function write($string)
    {
        $this->assertNotDetached();
        $this->string.= $string;
    }

    /**
     * @inheritdoc
     */
    public function read($length)
    {
        $this->assertNotDetached();
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getContents()
    {
        $this->assertNotDetached();
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getMetadata($key = null)
    {
        return $key === null ? array() : null;
    }

    /**
     * @throws RuntimeException If stream has been detached
     */
    protected function assertNotDetached()
    {
        if($this->string === null) {
            throw new RuntimeException("The stream has been detached");
        }
    }
}