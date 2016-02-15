<?php

namespace PHPixie\HTTP\Messages\Message\Request;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Base PSR-7 ServerRequest
 */
abstract class ServerRequest extends    \PHPixie\HTTP\Messages\Message\Request
                             implements ServerRequestInterface
{
    /**
     * @var array
     */
    protected $serverParams;

    /**
     * @var array
     */
    protected $queryParams;

    /**
     * @var mixed
     */
    protected $parsedBody;

    /**
     * @var array
     */
    protected $cookieParams;

    /**
     * @var array
     */
    protected $uploadedFiles;

    /**
     * @var array
     */
    protected $attributes = array();

    /**
     * @inheritdoc
     */
    public function getServerParams()
    {
        $this->requireServerParams();
        return $this->serverParams;
    }

    /**
     * @inheritdoc
     */
    public function getUploadedFiles()
    {
        $this->requireUploadedFiles();
        return $this->uploadedFiles;
    }

    /**
     * @inheritdoc
     */
    public function getCookieParams()
    {
        return $this->cookieParams;
    }

    /**
     * @inheritdoc
     */
    public function withCookieParams(array $cookies)
    {
        $new = clone $this;
        $new->cookieParams = $cookies;
        return $new;
    }

    /**
     * @inheritdoc
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * @inheritdoc
     */
    public function withQueryParams(array $query)
    {
        $new = clone $this;
        $new->queryParams = $query;
        return $new;
    }

    /**
     * @inheritdoc
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * @inheritdoc
     */
    public function withParsedBody($data)
    {
        $new = clone $this;
        $new->parsedBody = $data;
        return $new;
    }

    /**
     * @inheritdoc
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $new = clone $this;
        $new->uploadedFiles = $uploadedFiles;
        return $new;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @inheritdoc
     */
    public function getAttribute($attribute, $default = null)
    {
        if (!array_key_exists($attribute, $this->attributes)) {
            return $default;
        }
        return $this->attributes[$attribute];
    }

    /**
     * @inheritdoc
     */
    public function withAttribute($attribute, $value)
    {
        $new = clone $this;
        $new->attributes[$attribute] = $value;
        return $new;
    }

    /**
     * @inheritdoc
     */
    public function withoutAttribute($attribute)
    {
        $new = clone $this;
        if(array_key_exists($attribute, $new->attributes)) {
            unset($new->attributes[$attribute]);
        }
        return $new;
    }

    /**
     * @return void
     */
    protected function requireServerParams()
    {
    
    }

    /**
     * @return void
     */
    protected function requireUploadedFiles()
    {
    
    }
    
}