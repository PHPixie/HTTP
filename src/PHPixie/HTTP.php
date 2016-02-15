<?php

namespace PHPixie;

use PHPixie\HTTP\Messages\Message\Request;

/**
 * HTTP component
 */
class HTTP
{
    /**
     * @var HTTP\Builder
     */
    protected $builder;

    /**
     * Constructor
     * @param Slice $slice
     */
    public function __construct($slice)
    {
        $this->builder = $this->buildBuilder($slice);
    }

    /**
     * Create a PSR 7 ServerRequest from globals
     * @return Request\ServerRequest\SAPI
     */
    public function sapiServerRequest()
    {
        return $this->builder->messages()->sapiServerRequest();
    }

    /**
     * Create PHPixie request from server request.
     *
     * If the server request is not specified it will be created
     * from globals
     * @param Request\ServerRequest $serverRequest
     * @return HTTP\Request
     */
    public function request($serverRequest = null)
    {
        if ($serverRequest === null) {
            $serverRequest = $this->sapiServerRequest();
        }
        return $this->builder->request($serverRequest);
    }

    /**
     * Output a HTTP response
     * @param HTTP\Responses\Response $response
     * @param HTTP\Context $context Optional HTTP context to use (e.g. for cookie data)
     * @return void
     */
    public function output($response, $context = null)
    {
        $this->builder->output()->response($response, $context);
    }

    /**
     * Output a PSR-7 response message
     * @param HTTP\Responses\Response $responseMessage
     * @return void
     */
    public function outputResponseMessage($responseMessage)
    {
        $this->builder->output()->responseMessage($responseMessage);
    }

    /**
     * Create a context from a HTTP request
     * @param HTTP\Request $request
     * @param HTTP\Context\Session $session Optional session container,
     *        if not specified the default PHP session storage is used
     * @return HTTP\Context
     */
    public function context($request, $session = null)
    {
        $serverRequest = $request->serverRequest();
        $cookieArray = $serverRequest->getCookieParams();
        $cookies = $this->builder->cookies($cookieArray);
        if ($session === null) {
            $session = $this->builder->sapiSession();
        }

        return $this->builder->context($request, $cookies, $session);
    }

    /**
     * Create a context container
     * @param HTTP\Context $context
     * @return HTTP\Context\Container\Implementation
     */
    public function contextContainer($context)
    {
        return $this->builder->contextContainer($context);
    }

    /**
     * Message factory
     * @return HTTP\Messages
     */
    public function messages()
    {
        return $this->builder->messages();
    }

    /**
     * Response factory
     * @return HTTP\Responses
     */
    public function responses()
    {
        return $this->builder->responses();
    }

    /**
     * Get internal factory
     * @return HTTP\Builder
     */
    public function builder()
    {
        return $this->builder;
    }

    /**
     * @param Slice $slice
     * @return HTTP\Builder
     */
    protected function buildBuilder($slice)
    {
        return new HTTP\Builder($slice);
    }
}