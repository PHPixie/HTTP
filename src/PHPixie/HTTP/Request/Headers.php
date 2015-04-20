<?php

namespace PHPixie\HTTP\Request;

class Headers extends \PHPixie\Slice\Type\ArrayData
{
    public function getLine($key, $default = null)
    {
        $lines = $this->get($key);
    }
}