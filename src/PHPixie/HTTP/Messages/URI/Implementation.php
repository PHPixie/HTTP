<?php
namespace PHPixie\HTTP\Messages\URI;

class Implementation extends \PHPixie\HTTP\Messages\URI
{
    public function __construct($uri)
    {
        $parts = parse_url($uri);
        foreach($parts as $key => $value) {
            switch($key) {
                case 'port':
                    $value = (int) $value;
                    break;
                
                case 'path':
                    $value = $this->normalizePath($value);
                    break;
                
                case 'query':
                    $value = $this->normalizeQuery($value);
                    break;
                
                case 'fragment':
                    $value = $this->normalizeFragment($value);
                    break;
            }
            
            $this->parts[$key] = $value;
        }
    }
}