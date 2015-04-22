<?php

namespace PHPixie\HTTP\Context\Session;

class SAPI implements \PHPixie\HTTP\Context\Session
{
    protected $sessionReference;
    protected $sessionStarted = false;
    
    public function id()
    {
        $this->requireSession();
        return $this->sessionId();
    }
    
    public function setId($id)
    {
        $this->sessionId($id);
        $this->sessionStarted = false;
    }
    
    public function get($key, $default = null)
    {
        if($this->exists($key)) {
            return $this->sessionReference[$key];
        }
        
        return $default;
    }
    
    public function getRequired($key)
    {
        if($this->exists($key)) {
            return $this->sessionReference[$key];
        }
        
        throw new \PHPixie\HTTP\Exception("Session variable '$key' is not set");
    }
    
    public function exists($key)
    {
        $this->requireSession();
        return array_key_exists($key, $this->sessionReference);
    }
    
    
    public function set($key, $value)
    {
        $this->requireSession();
        $this->sessionReference[$key] = $value;
    }
    
    public function remove($key)
    {
        $this->requireSession();
        unset($this->sessionReference[$key]);
    }
    
    public function asArray()
    {
        $this->requireSession();
        return $this->sessionReference;
    }
    
    protected function requireSession()
    {
        if($this->sessionStarted === true) {
            return;
        }
        
        $this->sessionStart();
        $this->sessionStarted = true;
        $this->sessionReference = &$this->session();
    }
    
    protected function &session()
    {
        return $_SESSION;
    }
    
    protected function sessionStart()
    {
        session_start();
    }
    
    protected function sessionId($id = null)
    {
        if($id === null) {
            return session_id();
            
        }else{
            return session_id($id);
        }
    }
}