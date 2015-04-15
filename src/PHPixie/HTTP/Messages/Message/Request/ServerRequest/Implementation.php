<?php

namespace PHPixie\HTTP\Messages\Message\Request\ServerRequest;

class Implementation extends \PHPixie\HTTP\Messages\Message\Request\ServerRequest
{
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