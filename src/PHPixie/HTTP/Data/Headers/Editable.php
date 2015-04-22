<?php

namespace PHPixie\HTTP\Data\Headers;

class Editable extends \PHPixie\HTTP\Data\Headers
{
    public function set($name, $value)
    {
        $value = $this->normalizeValue($value);
        
        $this->remove($name);
        $this->setHeader($name, $value);
    }
    
    public function add($name, $value)
    {
        $this->requireNames();
        $value = $this->normalizeValue($value);
        
        $lower = strtolower($name);
        if(array_key_exists($lower, $this->names)) {
            $name = $this->names[$lower];
            foreach($value as $line) {
                $this->headers[$name][] = $line;
            }
        }else{
            $this->setHeader($name, $value);
        }
    }
    
    public function remove($name)
    {
        $this->requireNames();
        
        $lower = strtolower($name);
        if(array_key_exists($lower, $this->names)) {
            $name = $this->names[$lower];
            unset($this->names[$lower]);
            unset($this->headers[$name]);
        }
    }
    
    protected function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        $this->names[strtolower($name)] = $name;
    }
    
    protected function normalizeValue($value)
    {
        if(!is_array($value)) {
            return array($value);
        }
        
        return $value;
    }
}