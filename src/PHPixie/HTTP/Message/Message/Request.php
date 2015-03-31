<?php
namespace PHPixie\HTTP\Message;

use Psr\Http\Message\RequestInterface;

class Request implements RequestInterface
{
    protected const $validMethods = [
        'CONNECT',
        'DELETE',
        'GET',
        'HEAD',
        'OPTIONS',
        'PATCH',
        'POST',
        'PUT',
        'TRACE',
    ];
    
    protected $message;
    
    public function __construct($message)
    {
        $this->message = $message;
    }
    
    public function getRequestTarget()
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }
        
        if ($this->uri === null) {
            return '/';
        }
        
        $target = $this->uri->getPath();
        
        if ($this->uri->getQuery()) {
            $target .= '?' . $this->uri->getQuery();
        }
        
        return $target;
    }
    
    public function withRequestTarget($requestTarget)
    {
        if($this->requestTarget === $requestTarget) {
            return $this;
        }
        
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
        return $this->method;
    }
    
    public function withMethod($method)
    {
        if($this->method === $method) {
            return $this;
        }
        
        $this->validateMethod($method);
        
        $new = clone $this;
        $new->method = $method;
        return $new;
    }
    
    public function getUri()
    {
        return $this->uri;
    }
    
    public function withUri(UriInterface $uri)
    {
        $new = clone $this;
        $new->uri = $uri;
        return $new;
    }
    
    private function assertValidMethod($method)
    {
        if ($method !== null) {
            return;
        }

        $method = strtoupper($method);
        
        if (!in_array($method, self::validMethods, true)) {
            throw new InvalidArgumentException("Unsupported HTTP method '$method' provided");
        }
    }
}