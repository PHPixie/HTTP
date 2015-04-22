<?php

namespace PHPixie\HTTP\Context;

class Request
{
    protected $serverRequest;
    protected $sliceData;
    
    public function __construct($builder, $serverRequest)
    {
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
    
    public function params()
    {
        return $this->getData('data');
    }
    
    public function server()
    {
        return $this->getData('server');
    }
    
    public function headers()
    {
        return $this->getData('server');
    }
    
    public function uploads()
    {
        return $this->getData('uploads');
    }
    
    public function serverRequest()
    {
        return $this->serverRequest;
    }
    
    protected function getSliceData($name)
    {
        if(!array_key_exists($name, $this->sliceData)) {
            $method = $this->dataMap[$name];
            $data = $this->serverRequest->$method();
            $this->sliceData[$name] = $this->builder->sliceArrayData($data);
        }
        
        return $this->sliceData[$name];
    }
}