<?php

namespace PHPixie\HTTP\Context\Container;

interface Settable extends \PHPixie\HTTP\Context\Container
{
    public function setHttpContext($httpContext);
}
