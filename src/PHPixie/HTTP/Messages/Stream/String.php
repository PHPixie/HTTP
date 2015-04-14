<?php

namespace PHPixie\HTTP\Messages\Stream;

use Psr\Http\Message\StreamInterface;

class String implements StreamInterface
{
    protected $string;
    
    public function __construct($string = '')
    {
        $this->string = $string;
    }

    public function __toString()
    {
        return $this->string;
    }
    
    public function close()
    {
        $this->detach();
    }
    
    public function detach()
    {
        $this->string = '';
        return null;
    }

    public function getSize()
    {
        return mb_strlen($this->string, '8bit');
    }
    
    public function tell()
    {
        return false;
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
        return true;
    }
    
    public function isReadable()
    {
        return true;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        return false;
    }
    
    public function rewind()
    {
        return false;
    }
    
    public function write($string)
    {
        $this->string.= $string;
    }
    
    public function read($length)
    {
        return '';
    }
    
    
    public function getContents()
    {
        return '';
    }
    
    public function getMetadata($key = null)
    {
        return $key === null ? array() : null;
    }
}