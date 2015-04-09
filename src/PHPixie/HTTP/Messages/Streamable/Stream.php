<?php

namespace PHPixie\HTTP\Messages\Streamable;

use Psr\Http\Message\StreamableInterface;
use InvalidArgumentException;

class Stream implements StreamableInterface
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
    
    protected function resource()
    {
        if(!$this->processed) {
            
            if($this->resource !== false) {
                $this->resource = fopen($this->name, $this->mode);
            }
            
            $this->processed = true;
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
        if(($resource = $this->resource()) === null) {
            return false;
        }
        
        return ftell($resource);
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
            return false;
        }
        
        $result = fseek($this->resource(), $offset, $whence);
        return $result === 0;
    }
    
    public function rewind()
    {
        return $this->seek(0);
    }
    
    public function write($string)
    {
        if(!$this->isWritable()) {
            return false;
        }
        
        return fwrite($this->resource, $string);
    }
    
    public function read($length)
    {
        if(!$this->isReadable()) {
            return false;
        }
        
        return fread($this->resource(), $length);
    }
    
    public function getContents()
    {
        if(!$this->isReadable()) {
            return '';
        }
        
        return stream_get_contents($this->resource());
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