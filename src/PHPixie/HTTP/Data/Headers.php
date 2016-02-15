<?php

namespace PHPixie\HTTP\Data;

/**
 * Header storage
 */
class Headers
{
    /**
     * @var array
     */
    protected $headers = array();

    /**
     * @var array
     */
    protected $names;

    /**
     * Constructor
     * @param array $headers Headers map
     */
    public function __construct($headers = array())
    {
        foreach($headers as $key => $value) {
            $this->headers[$key] = $this->normalizeValue($value);
        }
    }

    /**
     * Get joined header values, or if missing return a default value
     * @param string $name
     * @param string $default
     * @return string
     */
    public function get($name, $default = '')
    {
        $lines = $this->getLines($name);
        if(count($lines) === 0) {
            return $default;
        }
        
        return $this->implode($lines);
    }

    /**
     * Get joined header values, or if missing throw an exception
     * @param string $name
     * @return string
     * @throw \PHPixie\HTTP\Exception
     */
    public function getRequired($name)
    {
        $lines = $this->getRequiredLines($name);
        return $this->implode($lines);
    }

    /**
     * Get header lines, or if missing return a default array
     * @param string $name
     * @param array $default
     * @return array
     */
    public function getLines($name, $default = array())
    {
        $name = $this->normalizeName($name);
        if(!$this->existsNormalized($name)) {
            return $default;
        }
        
        return $this->headers[$name];
    }

    /**
     * Get header lines, or if missing throw an exception
     * @param string $name
     * @return array
     * @throws \PHPixie\HTTP\Exception
     */
    public function getRequiredLines($name)
    {
        $name = $this->normalizeName($name);
        
        if(!$this->existsNormalized($name)) {
            throw new \PHPixie\HTTP\Exception("Header '$name' is not set");
        }
        
        return $this->headers[$name];
    }

    /**
     * Get headers array
     * @return array
     */
    public function asArray()
    {
        return $this->headers;
    }

    /**
     * @param string $normlizedName
     * @return bool
     */
    protected function existsNormalized($normlizedName)
    {
        return array_key_exists($normlizedName, $this->headers);
    }

    /**
     * @param array $lines
     * @return string
     */
    protected function implode($lines)
    {
        return implode(',', $lines);
    }

    /**
     * @param string $name
     * @return string
     */
    protected function normalizeName($name)
    {
        $this->requireNames();
        
        $lower = strtolower($name);
        
        if(array_key_exists($lower, $this->names)) {
            return $this->names[$lower];
        }
        
        return $name;
    }

    /**
     * @param string $value
     * @return array
     */
    protected function normalizeValue($value)
    {
        if(!is_array($value)) {
            return array($value);
        }
        
        return $value;
    }

    /**
     * @return void
     */
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