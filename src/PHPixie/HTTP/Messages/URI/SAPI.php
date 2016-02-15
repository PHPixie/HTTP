<?php

namespace PHPixie\HTTP\Messages\URI;

/**
 * URI implementation based on $_SERVER
 */
class SAPI extends \PHPixie\HTTP\Messages\URI
{
    /**
     * @var array
     */
    protected $server;

    /**
     * Constructor
     * @param array $server
     */
    public function __construct($server)
    {
        $this->server = $server;
    }

    /**
     * @param string $name
     */
    protected function requirePart($name)
    {
        switch($name) {
            case 'scheme':
                $this->requireScheme();
                break;
            
            case 'host':
            case 'port':
                $this->requireHostAndPort();
                break;
            
            case 'path':
                $this->requirePath();
                break;
            
            case 'query':
                $this->requireQuery();
                break;
            
            default:
                parent::requirePart($name);
        }
    }

    /**
     * @return void
     */
    protected function requireScheme()
    {
        $scheme = 'http';
        if(isset($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off') {
            $scheme = 'https';
        }
        
        $this->parts['scheme'] = $scheme;
    }

    /**
     * @return void
     */
    protected function requirePath()
    {
        $path = $this->server['REQUEST_URI'];
        $split = strpos($path, '?');
        
        if ($split !== false) {
            $path = substr($path, 0, $split);
        }
        
        $this->parts['path'] = $this->normalizePath($path);
    }

    /**
     * @return void
     */
    protected function requireQuery()
    {
        $query = ltrim($this->server['QUERY_STRING'], '?');
        $this->parts['query'] = $this->normalizeQuery($query);
    }

    /**
     * @return void
     */
    protected function requireHostAndPort()
    {
        $host = $this->server['HTTP_HOST'];
        $split = strrpos($host, ':');
        
        if($split !== false) {
            $port = (int) substr($host, $split+1);
            $host = substr($host, 0, $split);
            
        }else{
            $port = null;
        }
        
        if(!array_key_exists('host', $this->parts))
        {
            $this->parts['host'] = $host;
        }
        
        if(!array_key_exists('port', $this->parts))
        {
            $this->parts['port'] = $port;
        }
    }
    
    
}