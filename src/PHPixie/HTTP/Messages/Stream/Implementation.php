<?php

namespace PHPixie\HTTP\Messages\Stream;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

/**
 * PSR-7 Stream implementation
 */
class Implementation implements StreamInterface
{
    /**
     * @var string
     */
    protected $uri;

    /**
     * @var string
     */
    protected $mode;

    /**
     * @var resource
     */
    protected $resource;

    /**
     * @var bool
     */
    protected $processed = false;

    /**
     * @var bool
     */
    protected $isReadable;

    /**
     * @var bool
     */
    protected $isWritable;

    /**
     * @var bool
     */
    protected $isSeekable;

    /**
     * Constructor
     * @param string $uri Resource URI
     * @param string $mode File mode
     */
    public function __construct($uri, $mode = 'r')
    {
        $this->uri = $uri;
        $this->mode = $mode;
    }

    /**
     * @param bool $throwIfDetached Whether to throw
     *     an exception if resource was detached
     * @return resource
     * @throws RuntimeException
     */
    public function resource($throwIfDetached = false)
    {
        if(!$this->processed) {
            
            if($this->resource !== false) {
                $this->resource = fopen($this->uri, $this->mode);
            }
            
            $this->processed = true;
        }
        
        if($throwIfDetached && $this->resource === null) {
            throw new RuntimeException("The resource has been detached");
        }
        
        return $this->resource;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        if(($resource = $this->resource()) === null) {
            return '';
        }
        
        return stream_get_contents($resource, -1, 0);
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        $resource = $this->detach();
        
        if ($resource !== null) {
            fclose($resource);
        }
    }

    /**
     * @inheritdoc
     */
    public function detach()
    {
        $resource = $this->resource();
        $this->resource = null;
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function getSize()
    {
        if(($resource = $this->resource()) === null) {
            return null;
        }
        
        $stats = fstat($resource);
        return $stats['size'];
    }

    /**
     * @inheritdoc
     */
    public function tell()
    {
        return ftell($this->resource(true));
    }

    /**
     * @inheritdoc
     */
    public function eof()
    {
        if(($resource = $this->resource()) === null) {
            return true;
        }
        
        return feof($resource);
    }

    /**
     * @inheritdoc
     */
    public function isSeekable()
    {
        if(($resource = $this->resource()) === null) {
            return false;
        }
        
        if($this->isSeekable === null) {
            $meta = stream_get_meta_data($resource);
            $this->isSeekable = $meta['seekable'];
        }
        
        return $this->isSeekable;
    }

    /**
     * @inheritdoc
     */
    public function isWritable()
    {
        if(($resource = $this->resource()) === null) {
            return false;
        }
        
        if($this->isWritable === null) {
            $meta = stream_get_meta_data($resource);
            $this->isWritable = is_writable($meta['uri']);
        }
        
        return $this->isWritable;
    }

    /**
     * @inheritdoc
     */
    public function isReadable()
    {
        if(($resource = $this->resource()) === null) {
            return false;
        }
        
        if($this->isReadable === null) {
            $meta = stream_get_meta_data($resource);
            $mode = $meta['mode'];
            $this->isReadable = strstr($mode, 'r') || strstr($mode, '+');
        }
        
        return $this->isReadable;
    }

    /**
     * @inheritdoc
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if(!$this->isSeekable()) {
            throw new RuntimeException("The stream is not seakable");
        }
        
        fseek($this->resource(), $offset, $whence);
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
        if(!$this->isWritable()) {
            throw new RuntimeException("The stream is not writable");
        }
        
        return fwrite($this->resource(), $string);
    }

    /**
     * @inheritdoc
     */
    public function read($length)
    {
        if(!$this->isReadable()) {
            throw new RuntimeException("The stream is not readable");
        }
        
        return fread($this->resource(), $length);
    }

    /**
     * @inheritdoc
     */
    public function getContents()
    {
        return stream_get_contents($this->resource(true));
    }

    /**
     * @inheritdoc
     */
    public function getMetadata($key = null)
    {
        if(($resource = $this->resource()) === null) {
            $metadata = array();
            
        }else{
            $metadata = stream_get_meta_data($resource);
        }
        
        if ($key === null) {
            return $metadata;
        }
        
        if (!array_key_exists($key, $metadata)) {
            return null;
        }
        
        return $metadata[$key];
    }
    
    
}