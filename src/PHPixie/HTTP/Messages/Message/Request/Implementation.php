<?php

namespace PHPixie\HTTP\Messages\Message\Request;

class Implementation extends \PHPixie\HTTP\Messages\Message\Request
{
    public function __construct($protocolVersion, $headers, $body, $method, $uri)
    {
        $this->validateHeaders($headers);
        $this->validateMethod($method);
        
        $this->protocolVersion = $protocolVersion;
        $this->headers         = $headers;
        $this->body            = $body;
        $this->method          = $method;
        $this->uri             = $uri;
    }
}