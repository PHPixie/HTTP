<?php

namespace PHPixie\HTTP\Messages\Message\Request\ServerRequest;

class SAPI extends \PHPixie\HTTP\Messages\Message\Request\ServerRequest
{
    protected $http;
    protected $fileParams;
    
    public function __construct($http, $server, $get, $post, $cookies, $files, $attributes = array())
    {
        $this->messages     = $http;
        
        $this->serverParams = $server;
        $this->queryParams  = $get;
        $this->parsedBody   = $post;
        $this->cookieParams = $cookies;
        $this->fileParams   = $files;
        $this->attributes   = $attributes;
        
        $this->method       = $server['REQUEST_METHOD'];
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
    
    protected function requireUploadedFiles()
    {
        if($this->uploadedFiles !== null) {
            return;
        }
        
        $uploadedFiles = array();
        
        foreach($this->fileParams as $name => $data) {
            if(is_array($data['error'])) {
                $count  = count($data['error']);
                $keys   = array_keys($data);
                $values = array();
                
                for($i=0; $i<$count; $i++) {
                    $normalized = array();
                    foreach($keys as $key) {
                        $normalized[$key] = $data[$key][$i];
                    }
                    $values[] = $this->messages->sapiUploadedFile($normalized);
                }
                
                $uploadedFiles[$name] = $values;
            }else{
                $uploadedFiles[$name] = $this->messages->sapiUploadedFile($data);
            }
        }
        
        $this->uploadedFiles = $uploadedFiles;
    }

    protected function normalizeHeaderName($header)
    {
        $header = strtolower($header);
        $header = str_replace('_', ' ', $header);
        $header = ucwords($header);
        $header = str_replace(' ', '-', $header);
        return $header;
    }
    
    protected function requireBody()
    {
        if($this->body === null) {
            $this->body = $this->messages->stream('php://input');
        }
    }
    
    protected function requireUri()
    {
        if($this->uri === null) {
            $this->uri = $this->messages->sapiUri();
        }
    }
}