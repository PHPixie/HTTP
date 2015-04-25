<?php

namespace PHPixie\HTTP;

class Output
{
    public function response($response, $context = null)
    {
        $responseMessage = $response->asResponseMessage($context);
        $this->responseMessage($responseMessage);
    }
    
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
    
    protected function headers($headers)
    {
        foreach($headers as $name => $lines) {
            foreach($lines as $key => $line) {
                $this->header("$name: $line", false);
            }
        }
    }
    
    protected function header($header, $replace = true)
    {
        header($header, $replace);
    }
    
    protected function output($header)
    {
        echo $header;
    }
    
    protected function fpassthru($handle)
    {
        fpassthru($handle);
    }
    
    protected function body($stream)
    {
        if($stream instanceof \PHPixie\HTTP\Messages\Stream\Implementation) {
            $stream->rewind();
            $this->fpassthru($stream->resource());
            
        }else{
            $this->output((string) $stream);
        }
    }
    
    protected function statusHeader($statusCode, $reasonPhrase, $protocolVersion = '1.1')
    {
        if($protocolVersion === null) {
            $protocolVersion = '1.1';
        }
        
        $this->header("HTTP/{$protocolVersion} $statusCode $reasonPhrase");
    }
}