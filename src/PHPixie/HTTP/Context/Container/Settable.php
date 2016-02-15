<?php

namespace PHPixie\HTTP\Context\Container;

use PHPixie\HTTP\Context;

/**
 * Settable Container
 */
interface Settable extends \PHPixie\HTTP\Context\Container
{
    /**
     * @param Context $httpContext
     * @return void
     */
    public function setHttpContext($httpContext);
}
