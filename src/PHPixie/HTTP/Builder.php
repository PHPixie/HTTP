<?php

namespace PHPixie\HTTP;

class Builder
{
    protected $slice;
    protected $instances = array();
    
    public function __construct($slice) {
        $this->slice = $slice;
    }
    
    public function messages()
    {
        return $this->instance('messages');
    }
    
    public function responses()
    {
        return $this->instance('responses');
    }
    
    public function output()
    {
        return $this->instance('output');
    }
    
    public function request($serverRequest)
    {
        return new Request($this, $serverRequest);
    }
    
    public function data($array = array())
    {
        return $this->slice->arrayData($array);
    }
    
    public function headers($headerArray = array())
    {
        return new Data\Headers($headerArray);
    }
    
    public function editableHeaders($headerArray = array())
    {
        return new Data\Headers\Editable($headerArray);
    }
    
    public function serverData($serverData = array())
    {
        return new Data\Server($serverData);
    }
    
    public function context($request, $cookies, $session)
    {
        return new Context($request, $cookies, $session);
    }
    
    public function cookies($cookieArray = array())
    {
        return new Context\Cookies($this, $cookieArray);
    }
    
    public function sapiSession()
    {
        return new Context\Session\SAPI();
    }
    
    public function cookiesUpdate(
        $name,
        $value,
        $expires = null,
        $path = '/',
        $domain = null,
        $secure = false,
        $httpOnly = false
    )
    {
        return new Context\Cookies\Update(
            $name,
            $value,
            $expires,
            $path,
            $domain,
            $secure,
            $httpOnly
        );    
    }
    
    public function contextContainer($context)
    {
        return new Context\Container\Implementation($context);
    }
    
    protected function instance($name)
    {
        if(!array_key_exists($name, $this->instances)) {
            $method = 'build'.ucfirst($name);
            $this->instances[$name] = $this->$method();
        }
        
        return $this->instances[$name];
    }
    
    protected function buildMessages()
    {
        return new Messages();
    }
    
    protected function buildResponses()
    {
        return new Responses($this);
    }
    
    protected function buildOutput()
    {
        return new Output();
    }
}