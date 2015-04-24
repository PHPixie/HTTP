<?php

namespace PHPixie\HTTP\Responses;

class Response
{
    protected $messages;
    protected $headers;
    protected $statusCode = 200;
    protected $reasonPhrase;
    protected $body;
    
    public function __construct($messages, $headers, $body, $statusCode = 200, $reasonPhrase = null)
    {
        $this->messages     = $messages;
        $this->headers      = $headers;
        $this->body         = $body;
        $this->statusCode   = $statusCode;
        $this->reasonPhrase = $reasonPhrase;
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
    
    public function asResponseMessage($context = null)
    {
        return $this->messages->response(
            '1.1',
            $this->mergeContextHeaders($context),
            $this->body,
            $this->statusCode,
            $this->reasonPhrase
        );
    }
    
    protected function mergeContextHeaders($context)
    {
        $headers = $this->headers->asArray();
        
        if($context === null ) {
            return $headers;
        }
        
        $cookieUpdates = $context->cookies()->updates();
        if(empty($cookieUpdates)) {
            return $headers;
        }
        
        $cookieHeaders = array();
        foreach($cookieUpdates as $update) {
            $cookieHeaders[] = $update->asHeader();
        }
        
        foreach($headers as $name => $value) {
            if(strtolower($name) === 'set-cookie') {
                foreach($cookieHeaders as $header) {
                    $headers[$name][] = $header;
                }
                return $headers;
            }
        }
        
        $headers['Set-Cookie'] = $cookieHeaders;
        return $headers;
    }
}