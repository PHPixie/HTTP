<?php

namespace PHPixie\HTTP;
use Psr\Http\Message\StreamInterface;

/**
 * Used to output HTTP responses
 */
class Output
{
    /**
     * Output a HTTP response with optional context
     * @param Responses\Response $response
     * @param Context            $context
     * @return void
     */
    public function response($response, $context = null)
    {
        $responseMessage = $response->asResponseMessage($context);
        $this->responseMessage($responseMessage);
    }

    /**
     * Output a PSR 7 response message
     * @param Messages\Message\Response $responseMessage
     */
    public function responseMessage($responseMessage)
    {
        $this->statusHeader(
            $responseMessage->getStatusCode(),
            $responseMessage->getReasonPhrase(),
            $responseMessage->getProtocolVersion()
        );
        
        $this->headers($responseMessage->getHeaders());
        $this->body($responseMessage->getBody());
    }

    /**
     * Output headers
     * @param array $headers
     */
    protected function headers($headers)
    {
        foreach($headers as $name => $lines) {
            foreach($lines as $key => $line) {
                $this->header("$name: $line", false);
            }
        }
    }

    /**
     * @param $header
     * @param bool $replace
     */
    protected function header($header, $replace = true)
    {
        header($header, $replace);
    }

    /**
     * @param string $string
     */
    protected function output($string)
    {
        echo $string;
    }

    /**
     * @param resource $handle
     */
    protected function fpassthru($handle)
    {
        fpassthru($handle);
    }

    /**
     * @param StreamInterface $stream
     */
    protected function body($stream)
    {
        if($stream instanceof Messages\Stream\Implementation) {
            $stream->rewind();
            $this->fpassthru($stream->resource());
            
        }else{
            $this->output((string) $stream);
        }
    }

    /**
     * @param int    $statusCode
     * @param string $reasonPhrase
     * @param string $protocolVersion
     */
    protected function statusHeader($statusCode, $reasonPhrase, $protocolVersion = '1.1')
    {
        if($protocolVersion === null) {
            $protocolVersion = '1.1';
        }
        
        $this->header("HTTP/{$protocolVersion} $statusCode $reasonPhrase");
    }
}