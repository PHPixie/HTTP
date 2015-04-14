<?php

namespace PHPixie\Tests\HTTP\Messages\Message\Request\ServerRequest;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages\Message\Request\ServerRequest\Server
 */
class ServerTest extends \PHPixie\Tests\HTTP\Messages\Message\Request\ServerRequestTest
{
    protected $uri;
    protected $body;
    
    public function setUp()
    {
        $this->serverParams['REQUEST_METHOD']  = $this->method;
        $this->serverParams['SERVER_PROTOCOL'] = 'HTTP/'.$this->protocolVersion;
        
        foreach($this->headers as $key => $value) {
            $value = implode(',', $value);
            $this->headers[$key] = array($value);
            
            $key = 'HTTP_'.ucfirst(str_replace('-', '_', $key));
            $this->serverParams[$key] = $value;
        }
        
        $this->uri  = $this->abstractMock('\Psr\Http\Message\UriInterface');
        $this->body = $this->abstractMock('\Psr\Http\Message\StreamableInterface');
        
        parent::setUp();
    }
    
    public function message()
    {
        return new \PHPixie\HTTP\Messages\Message\Request\ServerRequest\Server(
            $this->serverParams,
            $this->uri,
            $this->body,
            $this->queryParams,
            $this->parsedBody,
            $this->cookieParams,
            $this->fileParams,
            $this->attributes
        );
    }    
}