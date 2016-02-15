<?php

namespace PHPixie\HTTP;

use PHPixie\HTTP\Data\Headers;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * HTTP request representation
 */
class Request
{
    /**
     * @var ServerRequestInterface
     */
    protected $serverRequest;

    /**
     * @var array
     */
    protected $dataMap = array();

    /**
     * @var array
     */
    protected $dataMethods = array(
        'query'      => 'getQueryParams',
        'data'       => 'getParsedBody',
        'attributes' => 'getAttributes',
        'uploads'    => 'getUploadedFiles',
    );

    /**
     * @var \PHPixie\Slice\Data
     */
    protected $server;

    /**
     * @var Headers
     */
    protected $headers;

    /**
     * Constructor
     * @param Builder $builder
     * @param ServerRequestInterface $serverRequest
     */
    public function __construct($builder, $serverRequest)
    {
        $this->builder       = $builder;
        $this->serverRequest = $serverRequest;
    }

    /**
     * Query parameters (e.g. $_GET)
     * @return \PHPixie\Slice\Type\ArrayData
     */
    public function query()
    {
        return $this->getData('query');
    }

    /**
     * Body parameters (e.g. $_POST)
     * @return \PHPixie\Slice\Type\ArrayData
     */
    public function data()
    {
        return $this->getData('data');
    }

    /**
     * Additional attributes (e.g. routing data)
     * @return \PHPixie\Slice\Type\ArrayData
     */
    public function attributes()
    {
        return $this->getData('attributes');
    }

    /**
     * Uploads data (e.g. $_FILES)
     * @return \PHPixie\Slice\Type\ArrayData
     */
    public function uploads()
    {
        return $this->getData('uploads');
    }

    /**
     * Server data (e.g. $_SERVER)
     * @return \PHPixie\HTTP\Data\Server
     */
    public function server()
    {
        if($this->server === null) {
            $data = $this->serverRequest->getServerParams();
            $this->server = $this->builder->serverData($data);
        }
        
        return $this->server;
    }

    /**
     * Headers
     * @return \PHPixie\HTTP\Data\Headers
     */
    public function headers()
    {
        if($this->headers === null) {
            $data = $this->serverRequest->getHeaders();
            $this->headers = $this->builder->headers($data);
        }
        
        return $this->headers;
    }

    /**
     * Get original PSR-7 ServerRequest
     * @return ServerRequestInterface
     */
    public function serverRequest()
    {
        return $this->serverRequest;
    }

    /**
     * Get HTTP method
     * @return string
     */
    public function method()
    {
        return $this->serverRequest->getMethod();
    }

    /**
     * PSR-7 URI of the request
     * @return UriInterface
     */
    public function uri()
    {
        return $this->serverRequest->getUri();
    }

    /**
     * @param string $type
     * @return \PHPixie\Slice\Type\ArrayData
     */
    protected function getData($type)
    {
        if(!array_key_exists($type, $this->dataMap)) {
            $method = $this->dataMethods[$type];
            $data = $this->serverRequest->$method();
            $this->dataMap[$type] = $this->builder->data($data);
        }
        
        return $this->dataMap[$type];
    }
}