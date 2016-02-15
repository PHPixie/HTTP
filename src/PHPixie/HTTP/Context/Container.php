<?php

namespace PHPixie\HTTP\Context;
use PHPixie\HTTP\Context;

/**
 * HTTP Context container
 */
interface Container
{
    /**
     * @return Context
     */
    public function httpContext();
}