<?php

namespace PHPixie\HTTP;

class Messages
{
    /**
     * @param $protocolVersion
     * @param $headers
     * @param $body
     * @return Messages\Message\Implementation
     */
    public function message($protocolVersion, $headers, $body)
    {
        return new Messages\Message\Implementation(
            $protocolVersion,
            $headers,
            $body
        );
    }

    /**
     * @param $protocolVersion
     * @param $headers
     * @param $body
     * @param $method
     * @param $uri
     * @return Messages\Message\Request\Implementation
     */
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

    /**
     * @param       $protocolVersion
     * @param       $headers
     * @param       $body
     * @param       $method
     * @param       $uri
     * @param       $serverParams
     * @param       $queryParams
     * @param       $parsedBody
     * @param       $cookieParams
     * @param       $uploadedFiles
     * @param array $attributes
     * @return Messages\Message\Request\ServerRequest\Implementation
     */
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
    ) {
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

    /**
     * @param null  $server
     * @param null  $get
     * @param null  $post
     * @param null  $cookie
     * @param null  $files
     * @param array $attributes
     * @return Messages\Message\Request\ServerRequest\SAPI
     */
    public function sapiServerRequest(
        $server = null,
        $get = null,
        $post = null,
        $cookie = null,
        $files = null,
        $attributes = array()
    ) {
        return new Messages\Message\Request\ServerRequest\SAPI(
            $this,
            $server !== null ? $server : $_SERVER,
            $get !== null ? $get : $_GET,
            $post !== null ? $post : $_POST,
            $cookie !== null ? $cookie : $_COOKIE,
            $files !== null ? $files : $_FILES,
            $attributes
        );
    }

    /**
     * @param      $protocolVersion
     * @param      $headers
     * @param      $body
     * @param int  $statusCode
     * @param null $reasonPhrase
     * @return Messages\Message\Response
     */
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

    /**
     * @param        $uri
     * @param string $mode
     * @return Messages\Stream\Implementation
     */
    public function stream($uri, $mode = 'r')
    {
        return new Messages\Stream\Implementation($uri, $mode);
    }

    /**
     * @param string $string
     * @return Messages\Stream\StringStream
     */
    public function stringStream($string = '')
    {
        return new Messages\Stream\StringStream($string);
    }

    /**
     * @param $uri
     * @return Messages\URI\Implementation
     */
    public function uri($uri)
    {
        return new Messages\URI\Implementation($uri);
    }

    /**
     * @param null $server
     * @return Messages\URI\SAPI
     */
    public function sapiUri($server = null)
    {
        return new Messages\URI\SAPI(
            $server !== null ? $server : $_SERVER
        );
    }

    /**
     * @param      $file
     * @param null $clientFilename
     * @param null $clientMediaType
     * @param null $size
     * @param int  $error
     * @return Messages\UploadedFile\Implementation
     */
    public function uploadedFile(
        $file,
        $clientFilename = null,
        $clientMediaType = null,
        $size = null,
        $error = UPLOAD_ERR_OK
    ) {
        return new Messages\UploadedFile\Implementation(
            $this,
            $file,
            $clientFilename,
            $clientMediaType,
            $size,
            $error
        );
    }

    /**
     * @param $fileData
     * @return Messages\UploadedFile\SAPI
     */
    public function sapiUploadedFile($fileData)
    {
        return new Messages\UploadedFile\SAPI($this, $fileData);
    }
}