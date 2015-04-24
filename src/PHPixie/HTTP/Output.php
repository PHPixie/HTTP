<?php

namespace PHPixie\HTTP;

class Output
{
    public function response($response, $context = null)
    {
        $this->outputStatusHeader(
            $response->statusCode(),
            $response->reasonPhrase()
        );
        
        $this->headers($response->headers()->asArray());
        if($context !== null) {
            $this->outputCookies($context->cookies);
        }
        $this->outputStream($response->body());
    }
    
    public function responseMessage($response)
    {
        $this->outputStatusHeader(
            $response->getStatusCode(),
            $response->getReasonPhrase(),
            $response->getProtocolVersion()
        );
        
        $this->headers($response->getHeaders());
        $this->outputStream($response->getBody());
    }
    
    protected function headers($headers)
    {
        foreach($headers as $name => $lines) {
            foreach($lines as $key => $line) {
                $this->header("$name: $line", $key === 0);
            }
        }
    }
    
    protected function header($header, $replace = true)
    {
        header($header, $replace);
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