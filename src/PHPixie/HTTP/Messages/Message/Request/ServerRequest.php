<?php

namespace PHPixie\HTTP\Messages\Message\Request;

use Psr\Http\Message\ServerRequestInterface;

abstract class ServerRequest extends    \PHPixie\HTTP\Messages\Message\Request
                             implements ServerRequestInterface
{
    
    protected $serverParams;
    protected $queryParams;
    protected $parsedBody;
    protected $cookieParams;
    protected $uploadedFiles;
    
    protected $attributes = array();
    
    public function getServerParams()
    {
        $this->requireServerParams();
        return $this->serverParams;
    }
    
    public function getUploadedFiles()
    {
        $this->requireUploadedFiles();
        return $this->uploadedFiles;
    }
    
    public function getCookieParams()
    {
        return $this->cookieParams;
    }
    
    public function withCookieParams(array $cookies)
    {
        $new = clone $this;
        $new->cookieParams = $cookies;
        return $new;
    }
    
    public function getQueryParams()
    {
        return $this->queryParams;
    }
    
    public function withQueryParams(array $query)
    {
        $new = clone $this;
        $new->queryParams = $query;
        return $new;
    }
    
    public function getParsedBody()
    {
        return $this->parsedBody;
    }
    
    public function withParsedBody($data)
    {
        $new = clone $this;
        $new->parsedBody = $data;
        return $new;
    }
    
    public function withUploadedFiles(array $uploadedFiles)
    {
        $new = clone $this;
        $new->uploadedFiles = $uploadedFiles;
        return $new;
    }
    
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    public function getAttribute($attribute, $default = null)
    {
        if (!array_key_exists($attribute, $this->attributes)) {
            return $default;
        }
        return $this->attributes[$attribute];
    }
    
    
    public function withAttribute($attribute, $value)
    {
        $new = clone $this;
        $new->attributes[$attribute] = $value;
        return $new;
    }
    
    public function withoutAttribute($attribute)
    {
        $new = clone $this;
        if(array_key_exists($attribute, $new->attributes)) {
            unset($new->attributes[$attribute]);
        }
        return $new;
    }
    
    protected function requireServerParams()
    {
    
    }
    
    protected function requireUploadedFiles()
    {
    
    }
    
}