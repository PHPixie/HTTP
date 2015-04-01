<?php

namespace PHPixie\HTTP\Messages;

use Psr\Http\Message\StreamableInterface;
use Psr\Http\Message\MessageInterface;

abstract class Message implements MessageInterface
{
    protected $protocolVersion;
    protected $headers;
    protected $body;
    
    protected $headerNames = array();
    
    protected function requireHeaders()
    {
        $this->populateHeaderNames();
    }
    
    protected function requireProtocolVersion(){}
    protected function requireBody(){}
    
    public function getProtocolVersion()
    {
        $this->requireProtocolVersion();
        return $this->protocolVersion;
    }

    public function withProtocolVersion($version)
    {
        $new = clone $this;
        $new->protocolVersion = $version;
        return $new;
    }

    public function getHeaders()
    {
        $this->requireHeaders();
        return $this->headers;
    }

    public function hasHeader($header)
    {
        $this->requireHeaders();
        $lower = strtolower($header);
        return array_key_exists($lower, $this->headerNames, true);
    }

    public function getHeader($header)
    {
        $this->requireHeaders();
        $lines = $this->getHeaderLines($header);
        
        if (count($lines) === 0) {
            return null;
        }

        return implode(',', $lines);
    }

    public function getHeaderLines($header)
    {
        $this->requireHeaders();
        $lower = strtolower($header);
        
        if(!array_key_exists($lower, $this->headerNames, true)) {
            return array();
        }
        
        $normalized = $this->headerNames[$lower];
        return $this->headers[$normalized];
    }

    public function withHeader($header, $value)
    {
        return $this->modifyHeader($header, $value);
    }

    public function withAddedHeader($header, $value)
    {
        return $this->modifyHeader($header, $value, true);
    }
    
    protected function modifyHeader($header, $value, $append = false)
    {
        $this->requireHeaders();
        if (!is_array($value)) {
            $value = array($value);
        }
        
        $headers = $this->headers;
        $lower = strtolower($header);
        
        if(array_key_exists($lower, $this->headerNames, true)) {
            $normalized = $this->headerNames[$lower];
            $currentValue = $headers[$normalized];
            
            if($append) {
                $value = array_merge($currentValue, $value);
                $header = $normalized;
            }else{
                unset($headers[$normalized]);
            }
            
            if($currentValue === $value) {
                return $this;
            }
        }
        
        $headers[$header] = $value;
        
        $new = clone $this;
        $new->headers = $headers;
        $new->headerNames[$lower] = $header;
        
        return $new;
    }

    public function withoutHeader($header)
    {
        $this->requireHeaders();
        $lower = strtolower($header);
        
        if(array_key_exists($lower, $this->headerNames, true)) {
            return $this;
        }
        
        $normalized = $this->headerNames[$lower];
        
        $new = clone $this;
        unset($new->headers[$normalized]);
        unset($new->headerNames[$lower]);
        
        return $new;
    }

    public function getBody()
    {
        $this->requireBody();
        return $this->body;
    }

    public function withBody(StreamableInterface $body)
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
    }
}