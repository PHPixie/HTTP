<?php

namespace PHPixie\HTTP;

class Responses
{
    protected $builder;
    
    public function __construct($builder)
    {
        $this->builder = $builder;   
    }
    
    public function string($string)
    {
        return $this->stringResponse($string);
    }
    
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
    
    public function json($data)
    {
        $string = json_encode($data);
        
        return $this->stringResponse(
            $string,
            array(
                'Cache-Control' => 'no-cache, must-revalidate',
                'Expires'       => 'Mon, 26 Jul 1997 05:00:00 GMT',
                'Content-type'  => 'application/json'
            )
        );
    }

    public function streamFile($file)
    {
        $body = $this->builder->messages()->stream($file);
        return $this->response($body);
    }
    
    public function download($fileName, $contentType, $contents)
    {
        $body = $this->builder->messages()->stringStream($contents);
        return $this->downloadResponse($fileName, $contentType, $body);
    }
    
    public function downloadFile($fileName, $contentType, $file)
    {
        $body = $this->builder->messages()->stream($file);
        return $this->downloadResponse($fileName, $contentType, $body);
    }
    
    public function response($body, $headerArray = array(), $statusCode = 200, $reasonPhrase = null)
    {
        $headers = $this->builder->editableHeaders($headerArray);
        return $this->buildResponse($headers, $body, $statusCode, $reasonPhrase);
    }
    
    protected function stringResponse($string, $headers = array(), $statusCode = 200)
    {
        $body = $this->builder->messages()->stringStream($string);
        return $this->response($body, $headers, $statusCode);
    }
    
    protected function downloadResponse($fileName, $contentType, $body)
    {
        $headers = array(
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"'
        );
        
        return $this->response($body, $headers);
    }
    
    protected function buildResponse($headers, $body, $statusCode = 200, $reasonPhrase = null)
    {
        return new Responses\Response($headers, $body, $statusCode, $reasonPhrase);
    }
}