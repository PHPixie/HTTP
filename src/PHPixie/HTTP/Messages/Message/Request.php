<?php
namespace PHPixie\HTTP\Messages\Message;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Base PSR-7 Request Implementation
 */
abstract class Request extends    \PHPixie\HTTP\Messages\Message
                       implements RequestInterface
{
    /**
     * @var array
     */
    protected static $validMethods = array(
        'CONNECT',
        'DELETE',
        'GET',
        'HEAD',
        'OPTIONS',
        'PATCH',
        'POST',
        'PUT',
        'TRACE',
    );

    /**
     * @var string
     */
    protected $requestTarget;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var UriInterface
     */
    protected $uri;

    /**
     * @inheritdoc
     */
    public function getRequestTarget()
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }
        
        $this->requireUri();
        
        if ($this->uri === null) {
            return '/';
        }
        
        $target = $this->uri->getPath();
        
        $query = $this->uri->getQuery();
        
        if ($query !== '') {
            $target .= '?'.$query;
        }
        
        $this->requestTarget = $target;
        
        return $this->requestTarget;
    }

    /**
     * @inheritdoc
     */
    public function withRequestTarget($requestTarget)
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new \InvalidArgumentException(
                'Invalid request target provided; cannot contain whitespace'
            );
        }

        $new = clone $this;
        $new->requestTarget = $requestTarget;
        return $new;
    }

    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        $this->requireMethod();
        return $this->method;
    }

    /**
     * @inheritdoc
     */
    public function withMethod($method)
    {
        $this->validateMethod($method);
        
        $new = clone $this;
        $new->method = $method;
        return $new;
    }

    /**
     * @inheritdoc
     */
    public function getUri()
    {
        $this->requireUri();
        return $this->uri;
    }

    /**
     * @inheritdoc
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $new = clone $this;
        $new->uri = $uri;
        if(!$preserveHost && ($host = $uri->getHost()) !== '') {
            $new->modifyHeader('Host', $host, false, false);
        }
        
        return $new;
    }

    /**
     * @param string $method
     * @throws \InvalidArgumentException
     */
    protected function validateMethod($method)
    {
        $method = strtoupper($method);
        
        if (!in_array($method, static::$validMethods, true)) {
            throw new \InvalidArgumentException("Unsupported HTTP method '$method' provided");
        }
    }

    /**
     * @return void
     */
    protected function requireMethod()
    {
        
    }

    /**
     * @return void
     */
    protected function requireUri()
    {
    
    }
}