<?php

namespace PHPixie\HTTP\Responses;

abstract class Response
{
    protected $headers;
    protected $statusCode;
    protected $statusMessage;
    
    public function __construct($headers)
    {
        $this->headers = $headers;
    }
    
    public function setStatus($code, $message = null)
    {
        $this->statusCode    = $code;
        $this->statusMessage = $message;
    }
    
}