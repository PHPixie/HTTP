<?php

namespace PHPixie\HTTP\Context\Container;

use PHPixie\HTTP\Context;

/**
 * Container implementation
 */
class Implementation implements \PHPixie\HTTP\Context\Container
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * Constructor
     * @param Context $context
     */
    public function __construct($context)
    {
        $this->context = $context;
    }

    /**
     * @inheritdoc
     */
    public function httpContext()
    {
        return $this->context;
    }
}
