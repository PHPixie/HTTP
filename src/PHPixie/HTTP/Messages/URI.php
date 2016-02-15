<?php

namespace PHPixie\HTTP\Messages;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

/**
 * Base PSR-7 URI implementation
 */
abstract class URI implements UriInterface
{
    /**
     * @var string
     */
    const CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';

    /**
     * @var string
     */
    const CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~';

    /**
     * generated uri string cache
     * @var string|null
     */
    protected $uriString;

    /**
     * @var array
     */
    protected $parts = array();

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function getScheme()
    {
        return $this->part('scheme');
    }

    /**
     * @inheritdoc
     */
    public function getUserInfo()
    {
        return $this->part('userInfo');
    }

    /**
     * @inheritdoc
     */
    public function getHost()
    {
        return $this->part('host');
    }

    /**
     * @inheritdoc
     */
    public function getPort()
    {
        $port = $this->part('port');

        if($this->isStandardPort($this->getScheme(), $port)) {
            $port = null;
        }

        return $port;
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return $this->part('path');
    }

    /**
     * @inheritdoc
     */
    public function getQuery()
    {
        return $this->part('query');
    }

    /**
     * @inheritdoc
     */
    public function getFragment()
    {
        return $this->part('fragment');
    }

    /**
     * @inheritdoc
     */
    public function withScheme($scheme)
    {
        $scheme = strtolower($scheme);
        $scheme = str_replace('://', '', $scheme);

        if (!in_array($scheme, array('', 'http', 'https'), true)) {
            throw new InvalidArgumentException("Unsupported scheme '$scheme', must be either 'http', 'https' or ''");
        }

        return $this->updatePart('scheme', $scheme);
    }

    /**
     * @inheritdoc
     */
    public function withUserInfo($user, $password = null)
    {
        $userInfo = $this->normalizePart($user);

        if ($userInfo !== '' && $password !== null) {
            $userInfo.= ':' . $password;
        }

        return $this->updatePart('userInfo', $userInfo);
    }

    /**
     * @param string $key
     * @param string $value
     * @return URI
     */
    protected function updatePart($key, $value)
    {
        $new = clone $this;
        $new->parts[$key] = $value;
        $new->uriString = null;

        return $new;
    }

    /**
     * @inheritdoc
     */
    public function withHost($host)
    {
        $host = $this->normalizePart($host);
        return $this->updatePart('host', $host);
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function withPath($path)
    {
        $path = $this->normalizePath($path);
        return $this->updatePart('path', $path);
    }

    /**
     * @inheritdoc
     */
    public function withQuery($query)
    {
        $query = $this->normalizeQuery($query);
        return $this->updatePart('query', $query);

    }

    /**
     * @inheritdoc
     */
    public function withFragment($fragment)
    {
        $fragment = $this->normalizeFragment($fragment);
        return $this->updatePart('fragment', $fragment);
    }

    /**
     * @param string $fragment
     * @return string
     */
    protected function normalizeFragment($fragment)
    {
        $fragment = $this->normalizePart($fragment, '#');
        return $this->normalizeQueryString($fragment);
    }

    /**
     * @param string $part
     * @param string|null $prefix
     * @return string
     */
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
     * @param string $scheme
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
     * @param string $query
     * @return string
     * @throws InvalidArgumentException
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

    /**
     * @param array $matches
     * @return string
     */
    protected function encodeMatchedQueryPart($matches) {
        return rawurlencode($matches[0]);
    }

    /**
     * @param string $name
     * @return string
     */
    protected function part($name)
    {
        if(!array_key_exists($name, $this->parts)) {
            $this->requirePart($name);
        }

        return $this->parts[$name];
    }

    /**
     * @param string $name
     */
    protected function requirePart($name)
    {
        if($name === 'port') {
            $this->parts['port'] = null;
        }else{
            $this->parts[$name] = '';
        }
    }
}
