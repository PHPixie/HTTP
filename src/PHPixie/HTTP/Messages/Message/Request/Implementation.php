<?php

namespace PHPixie\HTTP\Messages\Message\Request;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * PSR-7 Request implementation
 */
class Implementation extends \PHPixie\HTTP\Messages\Message\Request
{
    /**
     * Constructor
     * @param string $protocolVersion
     * @param array $headers
     * @param StreamInterface $body
     * @param string $method
     * @param UriInterface $uri
     */
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