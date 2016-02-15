<?php

namespace PHPixie\HTTP\Context\Cookies;

/**
 * Cookie update data
 */
class Update {

    /**
     * @var string
     */
    protected $name;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var int|null
     */
    protected $expires;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string|null
     */
    protected $domain;

    /**
     * @var bool
     */
    protected $secure;

    /**
     * @var bool
     */
    protected $httpOnly;

    /**
     * Constructor
     * @param string $name
     * @param mixed $value
     * @param int|null $expires
     * @param string $path
     * @param string|null $domain
     * @param bool $secure
     * @param bool $httpOnly
     */
    public function __construct(
        $name,
        $value,
        $expires = null,
        $path = '/',
        $domain = null,
        $secure = false,
        $httpOnly = false
    )
    {
        $this->name     = $name;
        $this->value    = $value;
        $this->expires  = $expires;
        $this->path     = $path;
        $this->domain   = $domain;
        $this->secure   = $secure;
        $this->httpOnly = $httpOnly;
    }

    /**
     * Name
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Value
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * Expires at (timestamp)
     * @return int|null
     */
    public function expires()
    {
        return $this->expires;
    }

    /**
     * Path
     * @return string
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * Domain
     * @return null|string
     */
    public function domain()
    {
        return $this->domain;
    }

    /**
     * Whether the cookie is secure
     * @return bool
     */
    public function secure()
    {
        return $this->secure;
    }

    /**
     * Whether the cookie is HTTP only
     * @return bool
     */
    public function httpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * Get header representation
     * @return string
     */
    public function asHeader()
    {
        $header = urlencode($this->name).'='.urlencode((string) $this->value);
        
        if($this->domain !== null) {
            $header.= '; domain='.$this->domain;
        }
        
        if($this->path !== null) {
            $header.= '; path='.$this->path;
        }
        
        if($this->expires !== null) {
            $header.= '; expires=' . gmdate('D, d-M-Y H:i:s e', $this->expires);
        }
        
        if($this->secure) {
            $header.= '; secure';
        }
        
        if($this->httpOnly) {
            $header.= '; HttpOnly';
        }
        
        return $header;
    }
}