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
            
            if($lifetime > 0) {
                $this->cookies[$name] = $value;
            }else {
                unset($this->cookies[$name]);
            }
            
        }else{
            $expires = null;
        }
        
        $this->updates[] = $this->builder->cookiesUpdate(
            $name,
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
    
    public function exists()
    {
        return array_key_exists($name, $this->cookies);
    }
    
    public function getUpdates()
    {
        return $this->updates;
    }
}