<?php

namespace PHPixie\HTTP;

class Builder
{
    protected $slice;
    protected $instances = array();
    
    public function __construct($slice) {
        $this->slice = $slice;
    }

    /**
     * @return Messages
     */
    public function messages()
    {
        return $this->instance('messages');
    }

    /**
     * @return Responses
     */
    public function responses()
    {
        return $this->instance('responses');
    }

    /**
     * @return Output
     */
    public function output()
    {
        return $this->instance('output');
    }

    /**
     * @param $serverRequest
     * @return \PHPixie\HTTP\Request
     */
    public function request($serverRequest)
    {
        return new Request($this, $serverRequest);
    }

    /**
     * @param array $array
     * @return mixed
     */
    public function data($array = array())
    {
        return $this->slice->arrayData($array);
    }

    /**
     * @param array $headerArray
     * @return Data\Headers
     */
    public function headers($headerArray = array())
    {
        return new Data\Headers($headerArray);
    }

    /**
     * @param array $headerArray
     * @return Data\Headers\Editable
     */
    public function editableHeaders($headerArray = array())
    {
        return new Data\Headers\Editable($headerArray);
    }

    /**
     * @param array $serverData
     * @return Data\Server
     */
    public function serverData($serverData = array())
    {
        return new Data\Server($serverData);
    }

    /**
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
     * @param array $cookieArray
     * @return Context\Cookies
     */
    public function cookies($cookieArray = array())
    {
        return new Context\Cookies($this, $cookieArray);
    }

    /**
     * @return Context\Session\SAPI
     */
    public function sapiSession()
    {
        return new Context\Session\SAPI();
    }

    /**
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
     * @param $context
     * @return Context\Container\Implementation
     */
    public function contextContainer($context)
    {
        return new Context\Container\Implementation($context);
    }
    
    protected function instance($name)
    {
        if(!array_key_exists($name, $this->instances)) {
            $method = 'build'.ucfirst($name);
            $this->instances[$name] = $this->$method();
        }
        
        return $this->instances[$name];
    }
    
    protected function buildMessages()
    {
        return new Messages();
    }
    
    protected function buildResponses()
    {
        return new Responses($this);
    }
    
    protected function buildOutput()
    {
        return new Output();
    }
}