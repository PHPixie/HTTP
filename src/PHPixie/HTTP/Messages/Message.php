<?php

namespace PHPixie\HTTP\Messages;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\MessageInterface;
use InvalidArgumentException;

abstract class Message implements MessageInterface
{
    protected $protocolVersion;
    protected $headers;
    protected $body;
    
    protected $processedHeaders = false;
    
    protected $headerNames = array();
    
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
        return array_key_exists($lower, $this->headerNames);
    }

    public function getHeaderLine($header)
    {
        $this->requireHeaders();
        $lines = $this->getHeader($header);
        
        if (count($lines) === 0) {
            return null;
        }

        return implode(',', $lines);
    }

    public function getHeader($header)
    {
        $this->requireHeaders();
        $lower = strtolower($header);
        
        if(!array_key_exists($lower, $this->headerNames)) {
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
    
    protected function modifyHeader($header, $value, $append = false, $clone = true)
    {
        $this->requireHeaders();
        if (!is_array($value)) {
            $value = array($value);
        }
        
        $headers = $this->headers;
        $lower = strtolower($header);
        
        if(array_key_exists($lower, $this->headerNames)) {
            $normalized = $this->headerNames[$lower];
            $currentValue = $headers[$normalized];
            
            if($append) {
                $value = array_merge($currentValue, $value);
                $header = $normalized;
            }else{
                unset($headers[$normalized]);
            }
        }
        
        $headers[$header] = $value;
        
        if($clone) {
            $message = clone $this;
            
        }else{
            $message = $this;
        }
        
        $message->headers = $headers;
        $message->headerNames[$lower] = $header;
        
        return $message;
    }

    public function withoutHeader($header)
    {
        $this->requireHeaders();
        $lower = strtolower($header);
        
        $new = clone $this;
        
        if(array_key_exists($lower, $this->headerNames)) {
            $normalized = $this->headerNames[$lower];
            unset($new->headers[$normalized]);
            unset($new->headerNames[$lower]);
        }
        
        return $new;
    }

    public function getBody()
    {
        $this->requireBody();
        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
    }
    
    protected function populateHeaderNames()
    {
        $headers = array_keys($this->headers);
        foreach($headers as $header) {
            $this->headerNames[strtolower($header)] = $header;
        }
    }
    
    protected function validateHeaders($headers)
    {
        foreach($headers as $name => $lines) {
            if(empty($lines)) {
                throw new InvalidArgumentException("Header values for '$name' are empty");
            }
        }
    }
    
    protected function requireHeaders()
    {
        if(!$this->processedHeaders) {
            $this->populateHeaderNames();
            $this->processedHeaders = true;
        }
    }
    
    protected function requireProtocolVersion()
    {
    
    }
    
    protected function requireBody()
    {
    
    }
}