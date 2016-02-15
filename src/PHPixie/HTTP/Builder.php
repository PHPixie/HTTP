<?php

namespace PHPixie\HTTP;

use PHPixie\Slice;

/**
 * HTTP factory
 */
class Builder
{
    /**
     * @var Slice
     */
    protected $slice;

    /**
     * @var array
     */
    protected $instances = array();

    /**
     * Builder constructor
     * @param Slice $slice
     */
    public function __construct($slice) {
        $this->slice = $slice;
    }

    /**
     * Message factory
     * @return Messages
     */
    public function messages()
    {
        return $this->instance('messages');
    }

    /**
     * Response factory
     * @return Responses
     */
    public function responses()
    {
        return $this->instance('responses');
    }

    /**
     * Output
     * @return Output
     */
    public function output()
    {
        return $this->instance('output');
    }

    /**
     * Build PHPixie request from PSR-7 ServerRequest
     * @param $serverRequest
     * @return Request
     */
    public function request($serverRequest)
    {
        return new Request($this, $serverRequest);
    }

    /**
     * Proxy for building slice data
     * @param array $array
     * @return Slice\Type\ArrayData
     */
    public function data($array = array())
    {
        return $this->slice->arrayData($array);
    }

    /**
     * Build headers storage
     * @param array $headerArray
     * @return Data\Headers
     */
    public function headers($headerArray = array())
    {
        return new Data\Headers($headerArray);
    }

    /**
     * Build editable headers storage
     * @param array $headerArray
     * @return Data\Headers\Editable
     */
    public function editableHeaders($headerArray = array())
    {
        return new Data\Headers\Editable($headerArray);
    }

    /**
     * Build server data storage
     * @param array $serverData
     * @return Data\Server
     */
    public function serverData($serverData = array())
    {
        return new Data\Server($serverData);
    }

    /**
     * Build HTTP context
     * @param Request $request
     * @param Context\Cookies $cookies
     * @param Context\Session\SAPI $session
     * @return Context
     */
    public function context($request, $cookies, $session)
    {
        return new Context($request, $cookies, $session);
    }

    /**
     * Build cookie storage
     * @param array $cookieArray
     * @return Context\Cookies
     */
    public function cookies($cookieArray = array())
    {
        return new Context\Cookies($this, $cookieArray);
    }

    /**
     * Build default session storage
     * @return Context\Session\SAPI
     */
    public function sapiSession()
    {
        return new Context\Session\SAPI();
    }

    /**
     * Build a single cookie update
     * @param            $name
     * @param            $value
     * @param null       $expires
     * @param string     $path
     * @param null       $domain
     * @param bool       $secure
     * @param bool       $httpOnly
     * @return Context\Cookies\Update
     */
    public function cookiesUpdate(
        $name,
        $value,
        $expires = null,
        $path = '/',
        $domain = null,
        $secure = false,
        $httpOnly = false
    )
    {
        return new Context\Cookies\Update(
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
     * Build context container
     * @param $context
     * @return Context\Container\Implementation
     */
    public function contextContainer($context)
    {
        return new Context\Container\Implementation($context);
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function instance($name)
    {
        if(!array_key_exists($name, $this->instances)) {
            $method = 'build'.ucfirst($name);
            $this->instances[$name] = $this->$method();
        }
        
        return $this->instances[$name];
    }

    /**
     * @return Messages
     */
    protected function buildMessages()
    {
        return new Messages();
    }

    /**
     * @return Responses
     */
    protected function buildResponses()
    {
        return new Responses($this);
    }

    /**
     * @return Output
     */
    protected function buildOutput()
    {
        return new Output();
    }
}