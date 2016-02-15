<?php

namespace PHPixie\HTTP;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Factory for PSR-7 implementations
 */
class Messages
{
    /**
     * Build a PSR-7 message
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
     * Build a PSR-7 request
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
     * Build a PSR-7 serverRequest
     * @param string            $protocolVersion
     * @param array             $headers
     * @param StreamInterface   $body
     * @param string            $method
     * @param UriInterface      $uri
     * @param array             $serverParams
     * @param array             $queryParams
     * @param null|array|object $parsedBody
     * @param array             $cookieParams
     * @param array             $uploadedFiles
     * @param array             $attributes
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
     * Build a server request from SAPI,
     * with the ability to override individual attributes
     * @param array|null $server
     * @param array|null $get
     * @param array|null $post
     * @param array|null $cookie
     * @param array|null $files
     * @param array      $attributes
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
     * Build a PSR-7 response
     * @param string          $protocolVersion
     * @param array           $headers
     * @param StreamInterface $body
     * @param int             $statusCode
     * @param string|null     $reasonPhrase
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
     * Build a PSR-7 stream
     * @param string $uri
     * @param string $mode
     * @return Messages\Stream\Implementation
     */
    public function stream($uri, $mode = 'r')
    {
        return new Messages\Stream\Implementation($uri, $mode);
    }

    /**
     * String stream
     * @param string $string
     * @return Messages\Stream\StringStream
     */
    public function stringStream($string = '')
    {
        return new Messages\Stream\StringStream($string);
    }

    /**
     * Build a PSR-7 URI from string
     * @param string $uri
     * @return Messages\URI\Implementation
     */
    public function uri($uri)
    {
        return new Messages\URI\Implementation($uri);
    }

    /**
     * Build a PSR-7 URI from SAPI globals,
     * with the ability to override $_SERVER data
     * @param array|null $server
     * @return Messages\URI\SAPI
     */
    public function sapiUri($server = null)
    {
        return new Messages\URI\SAPI(
            $server !== null ? $server : $_SERVER
        );
    }

    /**
     * Build a PSR-7 uploaded file representation
     * @param string      $file File path
     * @param string|null $clientFilename
     * @param string|null $clientMediaType
     * @param int|null    $size
     * @param int|int     $error
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
     * Build PSR-7 upload file representation from SAPI data
     * @param $fileData
     * @return Messages\UploadedFile\SAPI
     */
    public function sapiUploadedFile($fileData)
    {
        return new Messages\UploadedFile\SAPI($this, $fileData);
    }
}