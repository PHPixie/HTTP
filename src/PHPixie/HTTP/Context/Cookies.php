<?php

namespace PHPixie\HTTP\Context;

class Cookies
{
    protected $builder;
    
    protected $cookies;
    protected $updates = array();
    
    public function __construct($builder, $cookies = array())
    {
        $this->builder = $builder;
        $this->cookies = $cookies;
    }
    
    public function get($name, $default = null)
    {
        if($this->exists($name)) {
            return $this->cookies[$name];
        }
        
        return $default;
    }
    
    public function getRequired($name)
    {
        if($this->exists($name)) {
            return $this->cookies[$name];
        }
        
        throw new \PHPixie\HTTP\Exception("Cookie '$name' is not set");
    }
    
    public function set($name, $value, $lifetime = null, $path = '/', $domain = null, $secure = false, $httpOnly = false)
    {
        if($lifetime !== null) {
            $expires = time() + $lifetime;
            
        }else{
            $expires = null;
        }
        
        if($lifetime < 0) {
            unset($this->cookies[$name]);
            
        }else {
            $this->cookies[$name] = $value;            
        }
        
        $this->updates[$name] = $this->builder->cookiesUpdate(
            $name,
            $value,
            $expires,
            $path,
            $domain,
            $secure,
            $httpOnly
        );
    }
    
    public function remove($name)
    {
        $this->set($name, null, -3600*24*30);
    }
    
    public function exists($name)
    {
        return array_key_exists($name, $this->cookies);
    }
    
    public function updates()
    {
        return array_values($this->updates);
    }
    
    public function asArray()
    {
        return $this->cookies;
    }
}