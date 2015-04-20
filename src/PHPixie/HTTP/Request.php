<?php

namespace PHPixie\HTTP;

class Request
{
    protected $serverRequest;
    protected $dataMap;
    
    protected $dataMethods = array(
        'query'      => 'getQueryParams',
        'data'       => 'getParsedBody',
        'attributes' => 'getAttributes',
    );
    
    public function __construct($serverRequest)
    {
        $this->serverRequest =  $serverRequest;
    }
    
    public function query()
    {
        $this->getData('query');
    }
    
    public function data()
    {
        $this->getData('data');
    }
    
    public function attributes()
    {
        $this->getData('attributes');
    }
    
    public function server()
    {
       
    }
    

    
    public function header($name, $default = null)
    {
        return $this->serverRequest=?
    }
    
    
    protected f
    public function url()
    {
        return $this->
    }
}