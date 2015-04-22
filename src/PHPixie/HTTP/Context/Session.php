<?php

namespace PHPixie\HTTP\Context;

interface Session
{
    public function id();
    public function setId($id);
    
    public function get($key, $default = null);
    public function getRequired($key);
    public function exists($key);
    public function set($key, $value);
    public function remove($key);
    
    public function asArray();
}