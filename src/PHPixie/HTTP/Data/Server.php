<?php

namespace PHPixie\HTTP\Data;

class Server
{
    protected $server;
    
    public function __construct($server = array())
    {
        $this->server = $server;
    }
    
    public function get($key, $default = null)
    {
        $key = $this->normalizeKey($key);
        if(!$this->existsNormalized($key)) {
            return $default;
        }
        
        return $this->server[$key];
    }
    
    public function getRequired($key)
    {
        $key = $this->normalizeKey($key);
        if(!$this->existsNormalized($key)) {
            throw new \PHPixie\HTTP\Exception("server variable '$key' is not set");
        }
        
        return $this->server[$key];
    }
    
    public function asArray()
    {
        return $this->server;
    }
    
    protected function existsNormalized($normlizedKey)
    {
        return array_key_exists($normlizedKey, $this->server);
    }
    
    protected function normalizeKey($key)
    {
        return strtoupper($key);
    }
}