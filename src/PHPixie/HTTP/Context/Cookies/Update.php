<?php

namespace PHPixie\HTTP\Context\Cookies;

class Update {
    
    protected $name;
    protected $value;
    protected $expires;
    protected $path;
    protected $domain;
    protected $secure;
    protected $httpOnly;
    
    public function __construct($name, $value, $expires = null, $path = '/', $domain = null, $secure = false, $httpOnly = false)
    {
        $this->name     = $name;
        $this->value    = $value;
        $this->expires  = $expires;
        $this->path     = $path;
        $this->domain   = $domain;
        $this->secure   = $secure;
        $this->httpOnly = $httpOnly;
    }
    
    public function name()
    {
        return $this->name;
    }
    
    public function value()
    {
        return $this->value;
    }
    
    public function expires()
    {
        return $this->expires;
    }
    
    public function path()
    {
        return $this->path;
    }
    
    public function domain()
    {
        return $this->domain;
    }
    
    public function secure()
    {
        return $this->secure;
    }
    
    public function httpOnly()
    {
        return $this->httpOnly;
    }
}