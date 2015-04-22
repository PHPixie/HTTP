<?php

namespace PHPixie\HTTP\Data;

class Headers
{
    protected $headers = array();
    protected $names;
    
    public function __construct($headers = array())
    {
        foreach($headers as $key => $value) {
            $this->headers[$key] = $this->normalizeValue($value);
        }
    }
    
    public function get($name, $default = '')
    {
        $lines = $this->getLines($name);
        if(count($lines) === 0) {
            return $default;
        }
        
        return $this->implode($lines);
    }
    
    public function getRequired($name)
    {
        $lines = $this->getRequiredLines($name);
        return $this->implode($lines);
    }
    
    public function getLines($name, $default = array())
    {
        $name = $this->normalizeName($name);
        if(!$this->existsNormalized($name)) {
            return $default;
        }
        
        return $this->headers[$name];
    }
    
    public function getRequiredLines($name)
    {
        $name = $this->normalizeName($name);
        
        if(!$this->existsNormalized($name)) {
            throw new \PHPixie\HTTP\Exception("Header '$name' is not set");
        }
        
        return $this->headers[$name];
    }
    
    public function asArray()
    {
        return $this->headers;
    }
    
    protected function existsNormalized($normlizedName)
    {
        return array_key_exists($normlizedName, $this->headers);
    }
    
    protected function implode($lines)
    {
        return implode(',', $lines);
    }
    
    protected function normalizeName($name)
    {
        $this->requireNames();
        
        $lower = strtolower($name);
        
        if(array_key_exists($lower, $this->names)) {
            return $this->names[$lower];
        }
        
        return $name;
    }
    
    protected function requireNames()
    {
        if($this->names === null) {
            $this->names = array();
            foreach($this->headers as $name => $value) {
                $this->names[strtolower($name)] = $name;
            }
        }
    }
}