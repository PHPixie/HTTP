<?php

namespace PHPixie\HTTP;

class Request
{
    protected $serverRequest;
    protected $dataMap = array();
    protected $dataMethods = array(
        'query'      => 'getQueryParams',
        'data'       => 'getParsedBody',
        'attributes' => 'getAttributes',
        'uploads'    => 'getUploadedFiles',
    );
    protected $server;
    protected $headers;

    public function __construct($builder, $serverRequest)
    {
        $this->builder       = $builder;
        $this->serverRequest = $serverRequest;
    }

    /**
     * @return \PHPixie\Slice\Type\ArrayData
     */
    public function query()
    {
        return $this->getData('query');
    }

    /**
     * @return \PHPixie\Slice\Type\ArrayData
     */
    public function data()
    {
        return $this->getData('data');
    }

    /**
     * @return \PHPixie\Slice\Type\ArrayData
     */
    public function attributes()
    {
        return $this->getData('attributes');
    }

    /**
     * @return \PHPixie\Slice\Type\ArrayData
     */
    public function uploads()
    {
        return $this->getData('uploads');
    }

    /**
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
     * @return \PHPixie\HTTP\Messages\Message\Request\ServerRequest\SAPI
     */
    public function serverRequest()
    {
        return $this->serverRequest;
    }

    /**
     * @return string
     */
    public function method()
    {
        return $this->serverRequest->getMethod();
    }

    /**
     * @return \PHPixie\HTTP\Messages\URI\SAPI
     */
    public function uri()
    {
        return $this->serverRequest->getUri();
    }

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