<?php

namespace PHPixie\HTTP;

class Request
{
    protected $serverRequest;
    protected $dataMap = array();
    protected $dataMethods = array(
        'query'      => 'getQueryParams',
        'data'       => 'getParsedBody',
        'attributes' => 'getAttributes',
        'uploads'    => 'getUploadedFiles',
    );
    
    protected $server;
    protected $headers;
    
    public function __construct($builder, $serverRequest)
    {
        $this->builder       = $builder;
        $this->serverRequest = $serverRequest;
    }
    
    public function query()
    {
        return $this->getData('query');
    }
    
    public function data()
    {
        return $this->getData('data');
    }
    
    public function attributes()
    {
        return $this->getData('attributes');
    }
    
    public function uploads()
    {
        return $this->getData('uploads');
    }
    
    public function server()
    {
        if($this->server === null) {
            $data = $this->serverRequest->getServerParams();
            $this->server = $this->builder->serverData($data);
        }
        
        return $this->server;
    }
    
    public function headers()
    {
        if($this->headers === null) {
            $data = $this->serverRequest->getHeaders();
            $this->headers = $this->builder->headers($data);
        }
        
        return $this->headers;
    }
    
    public function serverRequest()
    {
        return $this->serverRequest;
    }
    
    public function method()
    {
        return $this->serverRequest->getMethod();
    }
    
    public function uri()
    {
        return $this->serverRequest->getUri();
    }
    
    protected function getData($type)
    {
        if(!array_key_exists($type, $this->dataMap)) {
            $method = $this->dataMethods[$type];
            $data = $this->serverRequest->$method();
            $this->dataMap[$type] = $this->builder->data($data);
        }
        
        return $this->dataMap[$type];
    }
}