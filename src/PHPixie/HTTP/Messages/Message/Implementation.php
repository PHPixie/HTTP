<?php

namespace PHPixie\HTTP\Messages\Message;

class Implementation extends \PHPixie\HTTP\Messages\Message
{
    protected $processedHeaders = false;
    
    public function __construct($protocolVersion, $headers, $stream)
    {
        $this->protocolVersion = $protocolVersion;
        $this->headers         = $headers;
        $this->strem           = $stream;
    }
    
    public function requireHeaders()
    {
        if($this->processedHeaders) {
            return;
        }
        
        $headerNames = array_keys($this->headers);
        foreach($headerNames as $header) {
            $this->headerNames[strtolower($header)] = $header;
        }
        
        $this->processedHeaders = true;
    }   
    
}