<?php

namespace PHPixie\HTTP\Messages\Message\Request\ServerRequest;

class Server extends \PHPixie\HTTP\Messages\Message\Request\ServerRequest
{
    public function __construct($server, $uri, $body, $get, $post, $cookies, $files, $attributes = array())
    {
        $this->serverParams    = $server;
        $this->uri             = $uri;
        $this->body            = $body;
        $this->queryParams     = $get;
        $this->parsedBody      = $post;
        $this->cookieParams    = $cookies;
        $this->fileParams      = $files;
        $this->attributes      = $attributes;
        
        $this->method          = $server['REQUEST_METHOD'];
    }
    
    protected function requireProtocolVersion()
    {
        if($this->protocolVersion === null) {
            $this->protocolVersion = substr($this->serverParams['SERVER_PROTOCOL'], 5);
        }
    }
    
    protected function requireHeaders()
    {
        if($this->processedHeaders) {
            return;
        }
        
        $headers = array();
        
        foreach($this->serverParams as $key => $value) {
            if($key === 'HTTP_COOKIE') {
                continue;
            }
            
            if (substr($key, 0, 5) === 'HTTP_') {
                $headerName = substr($key, 5);
                var_dump($headerName);
                $headerName = $this->normalizeHeaderName($headerName);
                $headers[$headerName] = array($value);
                
            }elseif(substr($key, 0, 8) === 'CONTENT_') {
                $headerName = substr($key, 8);
                $headerName = 'Content-' . $this->normalizeHeaderName($headerName);
                $headers[$headerName] = array($value);
            }
        }
        
        $this->headers = $headers;
        $this->populateHeaderNames();
        
        $this->processedHeaders = true;
    }

    protected function normalizeHeaderName($header)
    {
        $header = strtolower($header);
        $header = str_replace('_', ' ', $header);
        $header = ucwords($header);
        $header = str_replace(' ', '-', $header);
        return $header;
    }
}