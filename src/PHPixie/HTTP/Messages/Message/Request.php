<?php
namespace PHPixie\HTTP\Messages\Message;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use InvalidArgumentException;

class Request extends    \PHPixie\HTTP\Messages\Message
              implements RequestInterface
{
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
    
    protected $requestTarget;
    
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
    
    public function withRequestTarget($requestTarget)
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new InvalidArgumentException(
                'Invalid request target provided; cannot contain whitespace'
            );
        }

        $new = clone $this;
        $new->requestTarget = $requestTarget;
        return $new;
    }
    
    public function getMethod()
    {
        $this->requireMethod();
        return $this->method;
    }
    
    public function withMethod($method)
    {
        $this->validateMethod($method);
        
        $new = clone $this;
        $new->method = $method;
        return $new;
    }
    
    public function getUri()
    {
        $this->requireUri();
        return $this->uri;
    }
    
    public function withUri(UriInterface $uri)
    {
        $new = clone $this;
        $new->uri = $uri;
        return $new;
    }
    
    protected function validateMethod($method)
    {
        $method = strtoupper($method);
        
        if (!in_array($method, static::$validMethods, true)) {
            throw new InvalidArgumentException("Unsupported HTTP method '$method' provided");
        }
    }
    
    protected function requireMethod()
    {
        
    }
    
    protected function requireUri()
    {
    
    }
}