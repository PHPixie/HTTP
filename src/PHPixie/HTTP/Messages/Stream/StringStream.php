<?php

namespace PHPixie\HTTP\Messages\Stream;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

class StringStream implements StreamInterface
{
    protected $string;
    
    public function __construct($string = '')
    {
        $this->string = $string;
    }

    public function __toString()
    {
        return (string) $this->string;
    }
    
    public function close()
    {
        $this->detach();
    }
    
    public function detach()
    {
        $this->string = null;
        return null;
    }

    public function getSize()
    {
        if($this->string === null) {
            return null;
        }
        
        return mb_strlen($this->string, '8bit');
    }
    
    public function tell()
    {
        $this->assertNotDetached();
        return $this->getSize();
    }
    
    public function eof()
    {
        return true;
    }
    
    public function isSeekable()
    {
        return false;
    }
    
    public function isWritable()
    {
        if($this->string === null) {
            return false;
        }
        
        return true;
    }
    
    public function isReadable()
    {
        if($this->string === null) {
            return false;
        }
        
        return true;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        throw new RuntimeException("String streams are not seakable");
    }
    
    public function rewind()
    {
        return $this->seek(0);
    }
    
    public function write($string)
    {
        $this->assertNotDetached();
        $this->string.= $string;
    }
    
    public function read($length)
    {
        $this->assertNotDetached();
        return '';
    }
    
    
    public function getContents()
    {
        $this->assertNotDetached();
        return '';
    }
    
    public function getMetadata($key = null)
    {
        return $key === null ? array() : null;
    }
    
    protected function assertNotDetached()
    {
        if($this->string === null) {
            throw new RuntimeException("The stream has been detached");
        }
    }
}