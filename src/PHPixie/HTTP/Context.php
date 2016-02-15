<?php

namespace PHPixie\HTTP;

/**
 * Represents a HTTP context
 * containing a request and cookie and session storages
 */
class Context
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Context\Cookies
     */
    protected $cookies;

    /**
     * @var Context\Session
     */
    protected $session;

    /**
     * @param Request $request
     * @param Context\Cookies $cookies
     * @param Context\Session $session
     */
    public function __construct($request, $cookies, $session)
    {
        $this->request = $request;
        $this->cookies = $cookies;
        $this->session = $session;
    }

    /**
     * HTTP request
     * @return Request
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * Cookie storage
     * @return Context\Cookies
     */
    public function cookies()
    {
        return $this->cookies;
    }

    /**
     * Session storage
     * @return Context\Session
     */
    public function session()
    {
        return $this->session;
    }
}