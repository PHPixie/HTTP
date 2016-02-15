<?php

namespace PHPixie\HTTP\Context\Session;

/**
 * Standard PHP session storage
 */
class SAPI implements \PHPixie\HTTP\Context\Session
{
    /**
     * Session array by reference
     * @var array
     */
    protected $sessionReference;

    /**
     * @var bool
     */
    protected $sessionStarted = false;

    /**
     * @inheritdoc
     */
    public function id()
    {
        $this->requireSession();
        return $this->sessionId();
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->sessionId($id);
        $this->sessionStarted = false;
    }

    /**
     * @inheritdoc
     */
    public function get($key, $default = null)
    {
        if($this->exists($key)) {
            return $this->sessionReference[$key];
        }
        
        return $default;
    }

    /**
     * @inheritdoc
     */
    public function getRequired($key)
    {
        if($this->exists($key)) {
            return $this->sessionReference[$key];
        }
        
        throw new \PHPixie\HTTP\Exception("Session variable '$key' is not set");
    }

    /**
     * @inheritdoc
     */
    public function exists($key)
    {
        $this->requireSession();
        return array_key_exists($key, $this->sessionReference);
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value)
    {
        $this->requireSession();
        $this->sessionReference[$key] = $value;
    }

    /**
     * @inheritdoc
     */
    public function remove($key)
    {
        $this->requireSession();
        unset($this->sessionReference[$key]);
    }

    /**
     * @inheritdoc
     */
    public function asArray()
    {
        $this->requireSession();
        return $this->sessionReference;
    }

    /**
     * @return void
     */
    protected function requireSession()
    {
        if($this->sessionStarted === true) {
            return;
        }
        
        $this->sessionStart();
        $this->sessionStarted = true;
        $this->sessionReference = &$this->session();
    }

    /**
     * Refrence to the $_SESSION array
     * @return array
     */
    protected function &session()
    {
        return $_SESSION;
    }

    /**
     * @return void
     */
    protected function sessionStart()
    {
        session_start();
    }

    /**
     * @param string|null $id
     * @return string
     */
    protected function sessionId($id = null)
    {
        if($id === null) {
            return session_id();
            
        }else{
            return session_id($id);
        }
    }
}