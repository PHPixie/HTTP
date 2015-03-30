<?php
namespace Phly\Http;

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
class Uri implements UriInterface
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

    protected $fragments
    /**
     * generated uri string cache
     * @var string|null
     */
    private $uriString;

    /**
     * Function to urlencode the value returned by a regexp.
     * 
     * @var callable
     */
    private $urlEncode;


    public function __construct($uri = '')
    {
        if ($uri !== '') {
            $this->parseUri($uri);
        }
    }

    public function __clone()
    {
        $this->uriString = null;
    }

    public function __toString()
    {
        if ($this->uriString === null) {
            
            $uri = '';

            if(($authority = $this->getScheme()) !== '') {
                $uri .= $scheme.'://';
            }

            $uri .= $this->getAuthority();
            $uri .= $this->getPath();

            if($this->query !== null) {
                $uri .= '?'.$this->query;
            }

            if($this->fragment !== null) {
                $uri .= '?'.$this->fragment;
            }
            
            $this->uriString = $uri;
        }

        return $this->uriString;
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function getAuthority()
    {
        if ($this->host === '') {
            return '';
        }

        $authority = $this->host;
        
        if (! empty($this->userInfo)) {
            $authority = $this->userInfo . '@' . $authority;
        }

        if ($this->isNonStandardPort($this->scheme, $this->host, $this->port)) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    public function getUserInfo()
    {
        return $this->userInfo;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getFragment()
    {
        return $this->fragment;
    }

    public function withScheme($scheme)
    {
        $scheme = strtolower($scheme);
        $scheme = str_replace('://', '', $scheme);

        if ($scheme === $this->scheme) {
            return $this;
        }

        if (!in_array($scheme, ['', 'http', 'https'], true)) {
            throw new InvalidArgumentException("Unsupported scheme '$scheme', must be either 'http', 'https' or ''");
        }

        return $this->update('scheme', $value);
    }

    public function withUserInfo($user, $password = null)
    {
        $value = $user;
        if ($password) {
            $value .= ':' . $password;
        }
        
        return $this->checkUpdate('userInfo', $value);
    }
    
    protected function checkUpdate($key, $value)
    {
        if($this->$key === $value) {
            return $this;
        }
        
        return $this->update($key, $value);
    }
    
    protected function update($key, $value)
    {
        $new = clone $this;
        $new->$key = $value;

        return $new;
    }
    
    public function withHost($host)
    {
        return $this->checkUpdate('host', $host);
    }

    public function withPort($port)
    {
        if (!is_numeric($port)) {
            throw new InvalidArgumentException("Port '$port' is not numeric");
        }

        $port = (int) $port;

        if ($port === $this->port) {
            return $this;
        }

        if ($port < 1 || $port > 65535) {
            throw new InvalidArgumentException("Invalid port '$port' specified");
        }

        return $this->update('port', $port);
    }

    public function withPath($path)
    {
        if($this->path === $path) {
            return $this;
        }
        
        if (strpos($path, '?') !== false) {
            throw new InvalidArgumentException(
                'Invalid path provided; must not contain a query string'
            );
        }

        if (strpos($path, '#') !== false) {
            throw new InvalidArgumentException(
                'Invalid path provided; must not contain a URI fragment'
            );
        }

        $path = $this->filterPath($path);

        return $this->update('path', $path);
    }

    public function withQuery($query)
    {
        if($this->query === $query) {
            return $this;
        }
        
        if (strpos($query, '#') !== false) {
            throw new InvalidArgumentException(
                'Query string must not include a URI fragment'
            );
        }

        $query = $this->filterQuery($query);

        return $this->update('query', $query);
    }

    public function withFragment($fragment)
    {
        if($this->fragment === $fragment) {
            return $this;
        }
        
        $fragment = $this->filterFragment($fragment);
        return $this->update('fragment', $fragment);
    }

    private function parseUri($uri)
    {
        $parts = parse_url($uri);

        $this->scheme    = isset($parts['scheme'])   ? $parts['scheme']   : '';
        $this->userInfo  = isset($parts['user'])     ? $parts['user']     : '';
        $this->host      = isset($parts['host'])     ? $parts['host']     : '';
        $this->port      = isset($parts['port'])     ? $parts['port']     : null;
        $this->path      = isset($parts['path'])     ? $this->filterPath($parts['path']) : '';
        $this->query     = isset($parts['query'])    ? $this->filterQuery($parts['query']) : '';
        $this->fragment  = isset($parts['fragment']) ? $this->filterFragment($parts['fragment']) : '';

        if (isset($parts['pass'])) {
            $this->userInfo .= ':' . $parts['pass'];
        }
    }

    /**
     * Is a given port non-standard for the current scheme?
     *
     * @param string $scheme
     * @param string $host
     * @param int $port
     * @return bool
     */
    private static function isNonStandardPort($scheme, $host, $port)
    {
        if (! $scheme) {
            return true;
        }

        if (! $host || ! $port) {
            return false;
        }

        if ($scheme === 'https' && $port !== 443) {
            return true;
        }

        if ($scheme === 'http' && $port !== 80) {
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
    private function filterPath($path)
    {
        if ($path !== null && (empty($path) || substr($path, 0, 1) !== '/')) {
            $path = '/' . $path;
        }

        return preg_replace_callback(
            '/(?:[^' . self::CHAR_UNRESERVED . ':@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))/',
            $this->urlEncode,
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
    private function filterQuery($query)
    {
        if (! empty($query) && strpos($query, '?') === 0) {
            $query = substr($query, 1);
        }

        $parts = explode('&', $query);
        foreach ($parts as $index => $part) {
            list($key, $value) = $this->splitQueryValue($part);
            if ($value === null) {
                $parts[$index] = $this->filterQueryOrFragment($key);
                continue;
            }
            $parts[$index] = sprintf(
                '%s=%s',
                $this->filterQueryOrFragment($key),
                $this->filterQueryOrFragment($value)
            );
        }

        return implode('&', $parts);
    }

    /**
     * Split a query value into a key/value tuple.
     * 
     * @param string $value 
     * @return array A value with exactly two elements, key and value
     */
    private function splitQueryValue($value)
    {
        $data = explode('=', $value, 2);
        if (1 === count($data)) {
            $data[] = null;
        }
        return $data;
    }

    /**
     * Filter a fragment value to ensure it is properly encoded.
     * 
     * @param null|string $fragment 
     * @return string
     */
    private function filterFragment($fragment)
    {
        if (null === $fragment) {
            $fragment = '';
        }

        if (! empty($fragment) && strpos($fragment, '#') === 0) {
            $fragment = substr($fragment, 1);
        }

        return $this->filterQueryOrFragment($fragment);
    }

    /**
     * Filter a query string key or value, or a fragment.
     *
     * @param string $value
     * @return string
     */
    private function filterQueryOrFragment($value)
    {
        return preg_replace_callback(
            '/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/',
            $this->urlEncode,
            $value
        );
    }
}