<?php

namespace PHPixie\HTTP;

class Context
{
    protected $serverRequest;
    protected $cookies;
    protected $session;
    
    public function __construct($serverRequest, $cookies, $session)
    {
        $this->serverRequest = $serverRequest;
        $this->cookies       = $cookies;
        $this->session       = $session;
    }
    
    public function serverRequest()
    {
        return $this->serverRequest;
    }
    
    public function cookies()
    {
        return $this->cookies;
    }
    
    public function session()
    {
        return $this->session;
    }
}