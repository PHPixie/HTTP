<?php

namespace PHPixie\HTTP;
use PHPixie\HTTP\Data\Headers;
use Psr\Http\Message\StreamInterface;

/**
 * Response factory.
 */
class Responses
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Constructor
     * @param Builder $builder
     */
    public function __construct($builder)
    {
        $this->builder = $builder;   
    }

    /**
     * Response with a string body
     * @param $string
     * @return Responses\Response
     */
    public function string($string)
    {
        return $this->stringResponse($string);
    }

    /**
     * Redirect to a different url
     * @param $url
     * @param int $statusCode
     * @return Responses\Response
     */
    public function redirect($url, $statusCode = 302)
    {
        return $this->stringResponse(
            '',
            array(
                'Location' => $url
            ),
            $statusCode
        );
    }

    /**
     * JSON response
     *
     * Data will be automatically encoded to JSON
     * @param mixed $data
     * @return Responses\Response
     */
    public function json($data)
    {
        $string = json_encode($data, JSON_UNESCAPED_UNICODE);
        
        return $this->stringResponse(
            $string,
            array(
                'Cache-Control' => 'no-cache, must-revalidate',
                'Expires'       => 'Mon, 26 Jul 1997 05:00:00 GMT',
                'Content-type'  => 'application/json'
            )
        );
    }

    /**
     * Stream a file to the client
     * @param resource $file
     * @return Responses\Response
     */
    public function streamFile($file)
    {
        $body = $this->builder->messages()->stream($file);
        return $this->response($body);
    }

    /**
     * Download of contents as file
     * @param string $fileName
     * @param string $contentType
     * @param string $contents
     * @return Responses\Response
     */
    public function download($fileName, $contentType, $contents)
    {
        $body = $this->builder->messages()->stringStream($contents);
        return $this->downloadResponse($fileName, $contentType, $body);
    }

    /**
     * Download of a local file
     * @param string $fileName
     * @param string $contentType
     * @param resource $file
     * @return Responses\Response
     */
    public function downloadFile($fileName, $contentType, $file)
    {
        $body = $this->builder->messages()->stream($file);
        return $this->downloadResponse($fileName, $contentType, $body);
    }

    /**
     * Build a response
     * @param StreamInterface $body
     * @param array $headerArray
     * @param int $statusCode
     * @param string $reasonPhrase Status phrase
     * @return Responses\Response
     */
    public function response($body, $headerArray = array(), $statusCode = 200, $reasonPhrase = null)
    {
        $headers = $this->builder->editableHeaders($headerArray);
        return $this->buildResponse($headers, $body, $statusCode, $reasonPhrase);
    }

    /**
     * Build a string response
     * @param $string
     * @param array $headers
     * @param int $statusCode
     * @param string $reasonPhrase Status phrase
     * @return Responses\Response
     */
    protected function stringResponse($string, $headers = array(), $statusCode = 200, $reasonPhrase = null)
    {
        $body = $this->builder->messages()->stringStream($string);
        return $this->response($body, $headers, $statusCode, $reasonPhrase);
    }

    /**
     * Build a download response
     * @param string $fileName
     * @param string $contentType
     * @param StreamInterface $body
     * @return Responses\Response
     */
    protected function downloadResponse($fileName, $contentType, $body)
    {
        $headers = array(
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"'
        );
        
        return $this->response($body, $headers);
    }

    /**
     * @param Headers $headers
     * @param StreamInterface $body
     * @param int $statusCode
     * @param string $reasonPhrase
     * @return Responses\Response
     */
    protected function buildResponse($headers, $body, $statusCode = 200, $reasonPhrase = null)
    {
        $messages = $this->builder->messages();
        return new Responses\Response($messages, $headers, $body, $statusCode, $reasonPhrase);
    }
}
