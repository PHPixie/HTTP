<?php

namespace PHPixie\HTTP;

class Writer
{
    public function a()
    {
        $this->outputStatusHeader(
            $response->statusCode(),
            $response->reasonPhrase()
        );
        
        $this->outputHeaders($response->headers()->asArray());
        $this->outputCookies($context->cookies);
        $this->outputStream($response->body());
    }
    
    public function sapi($response)
    {
        $this->outputStatusHeader(
            $response->getStatusCode(),
            $response->getReasonPhrase(),
            $response->getProtocolVersion()
        );
        
        $this->outputHeaders($response->getHeaders());
        $this->outputStream($response->getBody());
    }
    
    protected function outputHeaders($headers)
    {
        foreach($headers as $name => $lines) {
            foreach($lines as $key => $line) {
                $this->outputHeader("$name: $line", $key === 0);
            }
        }
    }
    
    protected function outputHeader($header)
    {
        header($header);
    }
    
    protected function outputCookies($cookies)
    {
        foreach($cookies->getUpdates() as $update) {
            setcookie(
                $update->name(),
                $update->value(),
                $update->expires(),
                $update->path(),
                $update->domain(),
                $update->secure(),
                $update->httpOnly()
            );
        }
    }
    
    protected function outputStream($stream)
    {
        if($body instanceof \PHPixie\HTTP\Messages\Stream\Implementation) {
            fpassthru($body->detach());
            
        }else{
            echo (string) $body;
        }
    }
    
    protected function outputStatusHeader($statusCode, $reasonPhrase, $protocolVersion = '1.1')
    {
        if($protocolVersion === null) {
            $protocolVersion = '1.1';
            return "HTTP/{$protocolVersion} $statusCode $reasonPhrase";
        }
    }
}