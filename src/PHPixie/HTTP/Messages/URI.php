<?php

namespace PHPixie\HTTP\Messages;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

/**
 * Implementation of Psr\Http\UriInterface.
 *
 * Provides a value object representing a URI for HTTP requests.
 *
 * Instances of this class  are considered immutable; all methods that
 * might change state are implemented such that they retain the internal
 * state of the current instance and return a new instance that contains the
 * changed state.
 */
abstract class URI implements UriInterface
{
    /**
     * Sub-delimiters used in query strings and fragments.
     *
     * @const string
     */
    const CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';

    /**
     * Unreserved characters used in paths, query strings, and fragments.
     *
     * @const string
     */
    const CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~';
    
    /**
     * generated uri string cache
     * @var string|null
     */
    protected $uriString;

    protected $parts = array();

    public function __toString()
    {
        if ($this->uriString === null) {
            
            $uri = '';

            if(($scheme = $this->getScheme()) !== '') {
                $uri .= $scheme . '://';
            }

            $uri .= $this->getAuthority();
            $uri .= $this->getPath();

            if(($query = $this->getQuery()) !== '') {
                $uri .= '?' . $query;
            }

            if(($fragment = $this->getFragment()) !== '') {
                $uri .= '#' . $fragment;
            }
            
            $this->uriString = $uri;
        }
        
        return $this->uriString;
    }

    public function getAuthority()
    {
        $authority = '';
        $authority.= $this->getHost();
        
        if (($userInfo = $this->getUserInfo()) !== '') {
            $authority = $userInfo . '@' . $authority;
        }
        
        $port = $this->getPort();
        if ($port !== null) {
            $authority .= ':' . $port;
        }

        return $authority;
    }

    public function getScheme()
    {
        return $this->part('scheme');
    }
    
    public function getUserInfo()
    {
        return $this->part('userInfo');
    }

    public function getHost()
    {
        return $this->part('host');
    }

    public function getPort()
    {
        $port = $this->part('port');
        
        if($this->isStandardPort($this->getScheme(), $port)) {
            $port = null;
        }
        
        return $port;
    }

    public function getPath()
    {
        return $this->part('path');
    }

    public function getQuery()
    {
        return $this->part('query');
    }

    public function getFragment()
    {
        return $this->part('fragment');
    }

    public function withScheme($scheme)
    {
        $scheme = strtolower($scheme);
        $scheme = str_replace('://', '', $scheme);
        
        if (!in_array($scheme, ['', 'http', 'https'], true)) {
            throw new InvalidArgumentException("Unsupported scheme '$scheme', must be either 'http', 'https' or ''");
        }

        return $this->updatePart('scheme', $scheme);
    }

    public function withUserInfo($user, $password = null)
    {
        $userInfo = $this->normalizePart($user);
        
        if ($userInfo !== '' && $password !== null) {
            $userInfo.= ':' . $password;
        }
        
        return $this->updatePart('userInfo', $userInfo);
    }
    
    protected function updatePart($key, $value)
    {
        $new = clone $this;
        $new->parts[$key] = $value;
        $new->uriString = null;

        return $new;
    }
    
    public function withHost($host)
    {
        $host = $this->normalizePart($host);
        return $this->updatePart('host', $host);
    }

    public function withPort($port)
    {
        if($port !== null) {
            if (!is_numeric($port)) {
                throw new InvalidArgumentException("Port '$port' is not numeric");
            }
            
            $port = (int) $port;
            
            if ($port < 1 || $port > 65535) {
                throw new InvalidArgumentException("Invalid port '$port' specified");
            }
        }
        
        return $this->updatePart('port', $port);
    }

    public function withPath($path)
    {
        $path = $this->normalizePath($path);
        return $this->updatePart('path', $path);
    }

    public function withQuery($query)
    {
        $query = $this->normalizeQuery($query);
        return $this->updatePart('query', $query);

    }

    public function withFragment($fragment)
    {
        $fragment = $this->normalizeFragment($fragment);
        return $this->updatePart('fragment', $fragment);
    }
    
    protected function normalizeFragment($fragment)
    {
        $fragment = $this->normalizePart($fragment, '#');
        return $this->normalizeQueryString($fragment);
    }
    
    protected function normalizePart($part, $prefix = null) {
        if($part === null || $part === '') {
            return '';
        }
        
        if($prefix !== null && $part[0] === $prefix) {
            return substr($part, 1);
        }
        
        return $part;
    }
    
    /**
     * Is a given port non-standard for the current scheme?
     *
     * @param string $scheme
     * @param string $host
     * @param int $port
     * @return bool
     */
    protected static function isStandardPort($scheme, $port)
    {
        if ($scheme === 'https' && $port === 443) {
            return true;
        }

        if ($scheme === 'http' && $port === 80) {
            return true;
        }

        return false;
    }

    /**
     * Filters the path of a URI to ensure it is properly encoded.
     *
     * @param string $path
     * @return string
     */
    protected function normalizePath($path)
    {
        if (strpos($path, '?') !== false) {
            throw new InvalidArgumentException("Path '$path' contains '?'");
        }

        if (strpos($path, '#') !== false) {
            throw new InvalidArgumentException("Path '$path' contains '#'");
        }
         
        if ($path === null || $path === '') {
            return '/';
        }
        
        if($path[0] !== '/') {
            $path = '/' . $path;
        }

        return preg_replace_callback(
            '/(?:[^' . self::CHAR_UNRESERVED . ':@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))/',
            array($this, 'encodeMatchedQueryPart'),
            $path
        );
    }

    /**
     * Filter a query string to ensure it is propertly encoded.
     * 
     * Ensures that the values in the query string are properly urlencoded.
     * 
     * @param string $query 
     * @return string
     */
    protected function normalizeQuery($query)
    {
        if (strpos($query, '#') !== false) {
            throw new InvalidArgumentException(
                'Query string must not include a URI fragment'
            );
        }
        
        $query = $this->normalizePart($query, '?');        
        
        $pairs = explode('&', $query);
        
        foreach ($pairs as $pairKey => $pair) {
            $pair = explode('=', $pair, 2);
            foreach($pair as $key => $value) {
                $pair[$key] = $this->normalizeQueryString($value);
            }
            
            $parts[$pairKey] = implode('=', $pair);
        }

        return implode('&', $parts);
    }

    /**
     * Filter a query string key or value, or a fragment.
     *
     * @param string $value
     * @return string
     */
    protected function normalizeQueryString($value)
    {        
        return preg_replace_callback(
            '/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/',
            array($this, 'encodeMatchedQueryPart'),
            $value
        );
    }
    
    protected function encodeMatchedQueryPart($matches) {
        return rawurlencode($matches[0]);
    }
    
    protected function part($name)
    {
        if(!array_key_exists($name, $this->parts)) {
            $this->requirePart($name);
        }
        
        return $this->parts[$name];
    }
    
    protected function requirePart($name)
    {
        if($name === 'port') {
            $this->parts['port'] = null;
        }else{
            $this->parts[$name] = '';
        }
    }
}