<?php

namespace PHPixie\Tests\HTTP\Messages\Message\Request\ServerRequest;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages\Message\Request\ServerRequest\Implementation
 */
class ImplementationTest extends \PHPixie\Tests\HTTP\Messages\Message\Request\ServerRequestTest
{
    protected function message()
    {
        return new \PHPixie\HTTP\Messages\Message\Request\ServerRequest\Implementation(
            $this->protocolVersion,
            $this->headers,
            $this->body,
            $this->method,
            $this->uri,
            $this->serverParams,
            $this->queryParams,
            $this->parsedBody,
            $this->cookieParams,
            $this->uploadedFiles,
            $this->attributes
        );
    }    
}