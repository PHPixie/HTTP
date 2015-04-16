<?php

namespace PHPixie\HTTP\Context;

interface Session
{
    public function id();
    public function setId();
    
    public function get($name, $default = null);
    public function getRequired($name);
    public function exists($name);
    public function set($name, $value);
    public function remove($name);
    
    public function asArray();
}