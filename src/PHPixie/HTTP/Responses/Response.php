<?php

namespace PHPixie\HTTP\Responses;

class Response
{
    protected $headers;
    protected $statusCode = 200;
    protected $reasonPhrase;
    protected $body;
    
    public function __construct($headers, $body, $statusCode = 200, $reasonPhrase = null)
    {
        $this->headers = $headers;
        $this->body    = $body;
    }
    
    public function headers()
    {
        return $this->headers;
    }
    
    public function body()
    {
        return $this->body;
    }
    
    public function statusCode()
    {
        return $this->statusCode;
    }
    
    public function reasonPhrase()
    {
        return $this->reasonPhrase;
    }
    
    public function setStatus($code, $reasonPhrase = null)
    {
        $this->statusCode   = $code;
        $this->reasonPhrase = $reasonPhrase;
    }
}