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
            302
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
    
    public function stream($file)
    {
        return $this->fileResponse($file);   
    }

    public function download($string, $fileName, $contentType)
    {
        $body = $this->builder->messages()->stringStream($string);
        return $this->downloadResponse($body, $fileName, $contentType);   
    }
    
    public function downloadFile($file, $fileName, $contentType)
    {
        $body = $this->builder->messages()->stream($file);
        return $this->downloadResponse($body, $fileName, $contentType);   
    }
    
    public function response($body, $headers = array(), $statusCode = 200, $reasonPhrase = null)
    {
        $headers = $this->builder->headers($headers);
        return new Responses\Response($headers, $body, $statusCode, $reasonPhrase);
    }
    
    protected function downloadResponse($body, $fileName, $contentType)
    {
        return $this->response(
            $body,
            array(
                'Content-Type' => $coontentType,
                'Content-Disposition' => 'attachment; filename="'.$fileName.'"'
            )
        );
    }
    
    protected function stringResponse($string, $headers = array(), $statusCode = 200)
    {
        $body = $this->builder->messages()->stringStream($string);
        return $this->builder->response($body, $headers, $statusCode);
    }
}