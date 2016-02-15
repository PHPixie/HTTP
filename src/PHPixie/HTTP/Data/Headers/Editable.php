<?php

namespace PHPixie\HTTP\Data\Headers;

/**
 * Editable header storage
 */
class Editable extends \PHPixie\HTTP\Data\Headers
{
    /**
     * Set header replacing all headers with the same name
     * @param string $name
     * @param string|array $value
     */
    public function set($name, $value)
    {
        $value = $this->normalizeValue($value);
        
        $this->remove($name);
        $this->setHeader($name, $value);
    }

    /**
     * Add header value
     * @param string $name
     * @param string|array $value
     */
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

    /**
     * Remove all header values
     * @param string $name
     */
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

    /**
     * @param string $name
     * @param array $value
     */
    protected function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        $this->names[strtolower($name)] = $name;
    }
}