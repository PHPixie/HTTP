<?php
namespace PHPixie\HTTP\Messages\URI;

/**
 * PSR-7 URI Implementation
 */
class Implementation extends \PHPixie\HTTP\Messages\URI
{
    /**
     * Constructor
     * @param string $uri
     */
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