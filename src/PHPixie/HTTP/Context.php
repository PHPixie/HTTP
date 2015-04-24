<?php

namespace PHPixie\HTTP;

class Context
{
    protected $cookies;
    protected $session;
    
    public function __construct($cookies, $session)
    {
        $this->cookies = $cookies;
        $this->session = $session;
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