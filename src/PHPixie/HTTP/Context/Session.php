<?php

namespace PHPixie\HTTP\Context;

/**
 * Session storage
 */
interface Session
{
    /**
     * Session id
     * @return string|null
     */
    public function id();

    /**
     * Set session id
     * @param string $id
     * @return void
     */
    public function setId($id);

    /**
     * Get value by key, or if missing return the default value
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Get value by key, or if missing throw an exceptionn
     * @param mixed $key
     * @return mixed
     * @throws \PHPixie\HTTP\Exception
     */
    public function getRequired($key);

    /**
     * Check if key exists
     * @param mixed $key
     * @return bool
     */
    public function exists($key);

    /**
     * Set value by key
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value);

    /**
     * Set value by key
     * @param mixed $key
     * @return void
     */
    public function remove($key);

    /**
     * Get data as array
     * @return array
     */
    public function asArray();
}