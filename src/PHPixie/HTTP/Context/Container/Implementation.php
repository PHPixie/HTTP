<?php

namespace PHPixie\HTTP\Context\Container;

class Implementation implements \PHPixie\HTTP\Context\Container
{
    protected $context;
    
    public function __construct($context)
    {
        $this->context = $context;
    }
    
    public function httpContext()
    {
        return $this->context;
    }
}
