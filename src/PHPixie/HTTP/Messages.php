<?php

namespace PHPixie\HTTP;

class Messages
{
    public function message($protocolVersion, $headers, $body)
    {
        return new Messages\Message\Implementation(
            $protocolVersion,
            $headers,
            $body
        );
    }
    
    public function request($protocolVersion, $headers, $body, $method, $uri)
    {
        return new Messages\Message\Request\Implementation(
            $protocolVersion,
            $headers,
            $body,
            $method,
            $uri
        );
    }
    
    public function serverRequest(
        $protocolVersion,
        $headers,
        $body,
        $method,
        $uri,
        $serverParams,
        $queryParams,
        $parsedBody,
        $cookieParams,
        $uploadedFiles,
        $attributes = array()
    )
    {
        return new Messages\Message\Request\ServerRequest\Implementation(
            $protocolVersion,
            $headers,
            $body,
            $method,
            $uri,
            $serverParams,
            $queryParams,
            $parsedBody,
            $cookieParams,
            $uploadedFiles,
            $attributes
        );
    }
    
    public function sapiServerRequest(
        $server     = null,
        $get        = null,
        $post       = null,
        $cookie     = null,
        $files      = null,
        $attributes = array()
    )
    {   
        return new Messages\Message\Request\ServerRequest\SAPI(
            $this,
            $server !== null ? $server  : $_SERVER,
            $get    !== null ? $get     : $_GET,
            $post   !== null ? $post    : $_POST,
            $cookie !== null ? $cookie  : $_COOKIE,
            $files  !== null ? $files   : $_FILES,
            $attributes
        );
    }
    
   public function response($protocolVersion, $headers, $body, $statusCode = 200, $reasonPhrase = null)
    {
        return new Messages\Message\Response(
            $protocolVersion,
            $headers,
            $body,
            $statusCode,
            $reasonPhrase
        );
    }
    
    public function stream($uri, $mode = 'r')
    {
        return new Messages\Stream\Implementation($uri, $mode);
    }
    
    public function stringStream($string = '')
    {
        return new Messages\Stream\String($string);
    }
    
    public function uri($uri)
    {
        return new Messages\URI\Implementation($uri);
    }
    
    public function sapiUri($server = null)
    {        
        return new Messages\URI\SAPI(
            $server !== null ? $server : $_SERVER
        );
    }
    
    public function uploadedFile(
        $file,
        $clientFilename  = null,
        $clientMediaType = null,
        $size            = null,
        $error           = UPLOAD_ERR_OK
    )
    {
        return new Messages\UploadedFile\Implementation(
            $this,
            $file,
            $clientFilename,
            $clientMediaType,
            $size,
            $error
        );
    }
    
    public function sapiUploadedFile($fileData)
    {
        return new Messages\UploadedFile\SAPI($this, $fileData);
    }
}