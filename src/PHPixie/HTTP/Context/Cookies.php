<?php

namespace PHPixie\HTTP\Context;
use PHPixie\HTTP\Builder;

/**
 * Cookie storage
 */
class Cookies
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var array
     */
    protected $cookies;

    /**
     * @var array
     */
    protected $updates = array();

    /**
     * Constructor
     * @param Builder $builder
     * @param array $cookies Cookie data
     */
    public function __construct($builder, $cookies = array())
    {
        $this->builder = $builder;
        $this->cookies = $cookies;
    }

    /**
     * Get cookie, or if missing return the default value
     * @param string $name
     * @param mixed $default Default value
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if($this->exists($name)) {
            return $this->cookies[$name];
        }
        
        return $default;
    }

    /**
     * Get cookie or throw an exception if it's missing
     * @param string $name
     * @return mixed
     * @throws \PHPixie\HTTP\Exception
     */
    public function getRequired($name)
    {
        if($this->exists($name)) {
            return $this->cookies[$name];
        }
        
        throw new \PHPixie\HTTP\Exception("Cookie '$name' is not set");
    }

    /**
     * Set cookie
     *
     * See the PHP setcookie() function for more details
     * @param string $name
     * @param mixed $value
     * @param int|null $lifetime
     * @param string|null $path
     * @param string|null $domain
     * @param boolean $secure
     * @param bool $httpOnly
     * @return void
     */
    public function set(
        $name,
        $value,
        $lifetime = null,
        $path = '/',
        $domain = null,
        $secure = false,
        $httpOnly = false
    )
    {
        if($lifetime !== null) {
            $expires = time() + $lifetime;
            
        }else{
            $expires = null;
        }
        
        if($lifetime < 0) {
            unset($this->cookies[$name]);
            
        }else {
            $this->cookies[$name] = $value;            
        }
        
        $this->updates[$name] = $this->builder->cookiesUpdate(
            $name,
            $value,
            $expires,
            $path,
            $domain,
            $secure,
            $httpOnly
        );
    }

    /**
     * Remove a cookie
     * @param string $name
     * @return void
     */
    public function remove($name)
    {
        $this->set($name, null, -3600*24*30);
    }

    /**
     * Check if cookie exists
     * @param string $name
     * @return bool
     */
    public function exists($name)
    {
        return array_key_exists($name, $this->cookies);
    }

    /**
     * Get cookie updates
     * @return array
     */
    public function updates()
    {
        return array_values($this->updates);
    }

    /**
     * Get cookies as array
     * @return array
     */
    public function asArray()
    {
        return $this->cookies;
    }
}