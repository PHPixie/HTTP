<?php

namespace PHPixie\HTTP\Context\Session;

class SAPI implements \PHPixie\HTTP\Context\Session
{
    protected $id;
    
    public function id()
    {
        $this->requireSession();
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function get($name, $default = null)
    {
        if($this->exists($name)) {
            return $_SESSION[$name];
        }
        
        return $default;
    }
    
    public function getRequired($name)
    {
        if($this->exists($name)) {
            return $_SESSION[$name];
        }
        
        throw new \PHPixie\HTTP\Exception("Session variable '$name' is not set");
    }
    
    public function exists($name)
    {
        $this->requireSession();
        return array_key_exists($name, $_SESSION);
    }
    
    
    public function set($name, $value)
    {return $_SESSION[$name];
    
    }
    public function remove($name);
    
    public function asArray();
}