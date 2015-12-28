<?php

namespace PHPixie;

class HTTP
{
    protected $builder;
    
    public function __construct($slice)
    {
        $this->builder = $this->buildBuilder($slice);
    }

    /**
     * @return HTTP\Messages\Message\Request\ServerRequest\SAPI
     */
    public function sapiServerRequest()
    {
        return $this->builder->messages()->sapiServerRequest();
    }

    /**
     * @param HTTP\Messages\Message\Request\ServerRequest $serverRequest
     * @return HTTP\Request
     */
    public function request($serverRequest = null)
    {
        if($serverRequest === null) {
            $serverRequest = $this->sapiServerRequest();
        }
        return $this->builder->request($serverRequest);
    }

    /**
     * @param HTTP\Responses\Response $response
     * @param HTTP\Context $context
     */
    public function output($response, $context = null)
    {
        $this->builder->output()->response($response, $context);
    }

    /**
     * @param HTTP\Responses\Response $responseMessage
     */
    public function outputResponseMessage($responseMessage)
    {
        $this->builder->output()->responseMessage($responseMessage);
    }

    /**
     * @param HTTP\Request $request
     * @param HTTP\Context\Session\SAPI $session
     * @return HTTP\Context
     */
    public function context($request, $session = null)
    {
        $serverRequest = $request->serverRequest();
        $cookieArray = $serverRequest->getCookieParams();
        $cookies = $this->builder->cookies($cookieArray);
        if($session === null) {
            $session = $this->builder->sapiSession();
        }
        
        return $this->builder->context($request, $cookies, $session);
    }

    /**
     * @param HTTP\Context $context
     * @return HTTP\Context\Container\Implementation
     */
    public function contextContainer($context)
    {
        return $this->builder->contextContainer($context);
    }

    /**
     * @return HTTP\Messages
     */
    public function messages()
    {
        return $this->builder->messages();
    }

    /**
     * @return HTTP\Responses
     */
    public function responses()
    {
        return $this->builder->responses();
    }

    /**
     * @return HTTP\Builder
     */
    public function builder()
    {
        return $this->builder;
    }
    
    protected function buildBuilder($slice)
    {
        return new HTTP\Builder($slice);
    }
}