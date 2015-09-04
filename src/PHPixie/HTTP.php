<?php

namespace PHPixie;

class HTTP
{
    protected $builder;
    
    public function __construct($slice)
    {
        $this->builder = $this->buildBuilder($slice);
    }
    
    public function sapiServerRequest()
    {
        return $this->builder->messages()->sapiServerRequest();
    }
    
    public function request($serverRequest = null)
    {
        if($serverRequest === null) {
            $serverRequest = $this->sapiServerRequest();
        }
        return $this->builder->request($serverRequest);
    }
    
    public function output($response, $context = null)
    {
        $this->builder->output()->response($response, $context);
    }
    
    public function outputResponseMessage($responseMessage)
    {
        $this->builder->output()->responseMessage($responseMessage);
    }
    
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
    
    public function contextContainer($context)
    {
        return $this->builder->contextContainer($context);
    }
    
    public function messages()
    {
        return $this->builder->messages();
    }
    
    public function responses()
    {
        return $this->builder->responses();
    }
 
    public function builder()
    {
        return $this->builder;
    }
    
    protected function buildBuilder($slice)
    {
        return new HTTP\Builder($slice);
    }
}