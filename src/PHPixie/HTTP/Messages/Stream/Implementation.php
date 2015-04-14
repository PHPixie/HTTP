<?php

namespace PHPixie\HTTP\Messages\Stream;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

class Implementation implements StreamInterface
{
    protected $name;
    protected $mode;
    
    protected $resource;
    protected $processed = false;
    
    protected $isReadable;
    protected $isWritable;
    protected $isSeekable;
        
    public function __construct($name, $mode = 'r')
    {
        $this->name = $name;
        $this->mode = $mode;
    }
    
    protected function resource($throwIfDetached = false)
    {
        if(!$this->processed) {
            
            if($this->resource !== false) {
                $this->resource = fopen($this->name, $this->mode);
            }
            
            $this->processed = true;
        }
        
        if($throwIfDetached && $this->resource === null) {
            throw new RuntimeException("The resource has been detached");
        }
        
        return $this->resource;
    }

    public function __toString()
    {
        if(($resource = $this->resource()) === null) {
            return '';
        }
        
        return stream_get_contents($resource, -1, 0);
    }
    
    public function close()
    {
        $resource = $this->detach();
        
        if ($resource !== null) {
            fclose($resource);
        }
    }
    
    public function detach()
    {
        $resource = $this->resource();
        $this->resource = null;
        return $resource;
    }

    public function getSize()
    {
        if(($resource = $this->resource()) === null) {
            return null;
        }
        
        $stats = fstat($resource);
        return $stats['size'];
    }
    
    public function tell()
    {
        return ftell($this->resource(true));
    }
    
    public function eof()
    {
        if(($resource = $this->resource()) === null) {
            return true;
        }
        
        return feof($resource);
    }
    
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

    public function seek($offset, $whence = SEEK_SET)
    {
        if(!$this->isSeekable()) {
            throw new RuntimeException("The stream is not seakable");
        }
        
        fseek($this->resource(), $offset, $whence);
    }
    
    public function rewind()
    {
        $this->seek(0);
    }
    
    public function write($string)
    {
        if(!$this->isWritable()) {
            throw new RuntimeException("The stream is not writable");
        }
        
        return fwrite($this->resource(), $string);
    }
    
    public function read($length)
    {
        if(!$this->isReadable()) {
            throw new RuntimeException("The stream is not readable");
        }
        
        return fread($this->resource(), $length);
    }
    
    public function getContents()
    {
        return stream_get_contents($this->resource(true));
    }
    
    public function getMetadata($key = null)
    {
        if(($resource = $this->resource()) === null) {
            return null;
        }
        
        $metadata = stream_get_meta_data($resource);
        
        if ($key === null) {
            return $metadata;
        }
        
        if (!array_key_exists($key, $metadata)) {
            return null;
        }
        
        return $metadata[$key];
    }
}