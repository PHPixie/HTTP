<?php

namespace PHPixie\HTTP\Messages\Message\Request\ServerRequest;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * PSR-7 ServerRequest implementation
 */
class Implementation extends \PHPixie\HTTP\Messages\Message\Request\ServerRequest
{
    /**
     * Constructor
     * @param string $protocolVersion
     * @param array $headers
     * @param StreamInterface $body
     * @param string $method
     * @param UriInterface $uri
     * @param array $serverParams
     * @param array $queryParams
     * @param mixed $parsedBody
     * @param array $cookieParams
     * @param array $uploadedFiles
     * @param array $attributes
     */
    public function __construct(
        $protocolVersion,
        $headers,
        $body,
        $method,
        $uri,
        $serverParams,
        $queryParams,
        $parsedBody,
        $cookieParams,
        $uploadedFiles,
        $attributes
    )
    {
        $this->validateHeaders($headers);
        $this->validateMethod($method);
        
        $this->protocolVersion = $protocolVersion;
        $this->headers         = $headers;
        $this->body            = $body;
        $this->method          = $method;
        $this->uri             = $uri;
        
        $this->serverParams    = $serverParams;
        $this->queryParams     = $queryParams;
        $this->parsedBody      = $parsedBody;
        $this->cookieParams    = $cookieParams;
        $this->uploadedFiles   = $uploadedFiles;
        $this->attributes      = $attributes;
    }
}