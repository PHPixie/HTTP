<?php

namespace PHPixie\HTTP\Data;

/**
 * Server data storage
 */
class Server
{
    /**
     * @var array
     */
    protected $server;

    /**
     * Constructor
     * @param array $server Server data
     */
    public function __construct($server = array())
    {
        $this->server = $server;
    }

    /**
     * Get value, or if missing return a default one
     * @param string $key
     * @param string|null $default
     * @return string
     */
    public function get($key, $default = null)
    {
        $key = $this->normalizeKey($key);
        if(!$this->existsNormalized($key)) {
            return $default;
        }
        
        return $this->server[$key];
    }

    /**
     * Get value, or if missing throw an exception
     * @param string $key
     * @return string
     * @throws \PHPixie\HTTP\Exception
     */
    public function getRequired($key)
    {
        $key = $this->normalizeKey($key);
        if(!$this->existsNormalized($key)) {
            throw new \PHPixie\HTTP\Exception("server variable '$key' is not set");
        }
        
        return $this->server[$key];
    }

    /**
     * Get data as array
     * @return array
     */
    public function asArray()
    {
        return $this->server;
    }

    /**
     * @param string $normlizedKey
     * @return bool
     */
    protected function existsNormalized($normlizedKey)
    {
        return array_key_exists($normlizedKey, $this->server);
    }

    /**
     * @param string $key
     * @return string
     */
    protected function normalizeKey($key)
    {
        return strtoupper($key);
    }
}