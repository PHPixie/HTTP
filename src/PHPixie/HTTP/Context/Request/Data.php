<?php

namespace PHPixie\HTTP\Context\Request;

class Data extends \PHPixie\Slice\Data\ArrayData
{
    public function __get($key)
    {
        return $this->get($key);
    }
}