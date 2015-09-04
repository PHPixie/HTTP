<?php

namespace PHPixie\HTTP;

class Context
{
    protected $request;
    protected $cookies;
    protected $session;
    
    public function __construct($request, $cookies, $session)
    {
        $this->request = $request;
        $this->cookies = $cookies;
        $this->session = $session;
    }
    
    public function request()
    {
        return $this->request;
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