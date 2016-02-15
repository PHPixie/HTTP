<?php

namespace PHPixie\HTTP\Messages\Message\Request\ServerRequest;

use PHPixie\HTTP\Messages;

/**
 * PSR-7 ServerRequest from PHP globals data
 */
class SAPI extends \PHPixie\HTTP\Messages\Message\Request\ServerRequest
{
    /**
     * @var Messages
     */
    protected $messages;

    /**
     * @var array
     */
    protected $fileParams;

    /**
     * Constructor
     * @param Messages $messages
     * @param array $server
     * @param array $get
     * @param array $post
     * @param array $cookies
     * @param array $files
     * @param array $attributes
     */
    public function __construct($messages, $server, $get, $post, $cookies, $files, $attributes = array())
    {
        $this->messages     = $messages;
        
        $this->serverParams = $server;
        $this->queryParams  = $get;
        $this->parsedBody   = $post;
        $this->cookieParams = $cookies;
        $this->fileParams   = $files;
        $this->attributes   = $attributes;
        
        $this->method       = $server['REQUEST_METHOD'];
    }

    /**
     * @inheritdoc
     */
    protected function requireProtocolVersion()
    {
        if($this->protocolVersion === null) {
            $this->protocolVersion = substr($this->serverParams['SERVER_PROTOCOL'], 5);
        }
    }

    /**
     * @inheritdoc
     */
    protected function requireHeaders()
    {
        if($this->processedHeaders) {
            return;
        }
        
        $headers = array();
        
        foreach($this->serverParams as $key => $value) {
            if($key === 'HTTP_COOKIE') {
                continue;
            }
            
            if (substr($key, 0, 5) === 'HTTP_') {
                $headerName = substr($key, 5);
                $headerName = $this->normalizeHeaderName($headerName);
                $headers[$headerName] = array($value);
                
            }elseif(substr($key, 0, 8) === 'CONTENT_') {
                $headerName = substr($key, 8);
                $headerName = 'Content-' . $this->normalizeHeaderName($headerName);
                $headers[$headerName] = array($value);
            }
        }
        
        $this->headers = $headers;
        $this->populateHeaderNames();
        
        $this->processedHeaders = true;
    }

    /**
     * @inheritdoc
     */
    protected function requireUploadedFiles()
    {
        if($this->uploadedFiles !== null) {
            return;
        }
        
        $uploadedFiles = array();
        
        foreach($this->fileParams as $name => $data) {
            if(is_array($data['error'])) {
                $count  = count($data['error']);
                $keys   = array_keys($data);
                $values = array();
                
                for($i=0; $i<$count; $i++) {
                    $normalized = array();
                    foreach($keys as $key) {
                        $normalized[$key] = $data[$key][$i];
                    }
                    $values[] = $this->messages->sapiUploadedFile($normalized);
                }
                
                $uploadedFiles[$name] = $values;
            }else{
                $uploadedFiles[$name] = $this->messages->sapiUploadedFile($data);
            }
        }
        
        $this->uploadedFiles = $uploadedFiles;
    }

    /**
     * @param string $header
     * @return string
     */
    protected function normalizeHeaderName($header)
    {
        $header = strtolower($header);
        $header = str_replace('_', ' ', $header);
        $header = ucwords($header);
        $header = str_replace(' ', '-', $header);
        return $header;
    }

    /**
     * @inheritdoc
     */
    protected function requireBody()
    {
        if($this->body === null) {
            $this->body = $this->messages->stream('php://input');
        }
    }

    /**
     * @inheritdoc
     */
    protected function requireUri()
    {
        if($this->uri === null) {
            $this->uri = $this->messages->sapiUri($this->serverParams);
        }
    }
}