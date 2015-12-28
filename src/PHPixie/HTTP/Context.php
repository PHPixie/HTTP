<?php

namespace PHPixie\HTTP;

class Context
{
    protected $request;
    protected $cookies;
    protected $session;

    /**
     * @param Request $request
     * @param Context\Cookies $cookies
     * @param Context\Session\SAPI $session
     */
    public function __construct($request, $cookies, $session)
    {
        $this->request = $request;
        $this->cookies = $cookies;
        $this->session = $session;
    }

    /**
     * @return Request
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * @return Context\Cookies
     */
    public function cookies()
    {
        return $this->cookies;
    }

    /**
     * @return Context\Session\SAPI
     */
    public function session()
    {
        return $this->session;
    }
}