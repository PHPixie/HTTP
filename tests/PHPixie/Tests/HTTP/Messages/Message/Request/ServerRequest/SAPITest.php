<?php

namespace PHPixie\Tests\HTTP\Messages\Message\Request\ServerRequest;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages\Message\Request\ServerRequest\SAPI
 */
class SAPITest extends \PHPixie\Tests\HTTP\Messages\Message\Request\ServerRequestTest
{
    protected $http;
    protected $uri;
    protected $body;

    public function setUp()
    {
        $this->messages = $this->quickMock('\PHPixie\HTTP\Messages');
        
        $this->serverParams['HTTP_COOKIE']     = 'test';
        $this->serverParams['REQUEST_METHOD']  = $this->method;
        $this->serverParams['SERVER_PROTOCOL'] = 'HTTP/'.$this->protocolVersion;
        
        foreach($this->headers as $key => $value) {
            $value = implode(',', $value);
            $this->headers[$key] = array($value);
            
            $key = 'HTTP_'.ucfirst(str_replace('-', '_', $key));
            $this->serverParams[$key] = $value;
        }
        
        $this->serverParams['CONTENT_TYPE'] = 'text';
        $this->headers['Content-Type'] = array('text');
        
        parent::setUp();
        
        $this->method($this->messages, 'sapiUri', $this->uri, array());
        $this->method($this->messages, 'stream', $this->body, array('php://input'));
    }
    
    protected function getFileParams()
    {
        $fileParams = array();
        $fileParamsFlat = array();
        $uploadedFiles = array();
        
        foreach($this->uploadedFiles as $name => $file) {
            if(!is_array($file)) {
                $param = $this->getFileData($name);
                $fileParams[$name] = $param;
                $fileParamsFlat[] = $param;
                $uploadedFiles[] = $file;
                
            }else{
                $params = array();
                foreach($file as $key => $value) {
                    $param = $this->getFileData($name.$key);
                    $fileParamsFlat[] = $param;
                    $params[]         = $param;
                    $uploadedFiles[]  = $value;
                }
                
                $merged = array_fill_keys(array_keys($param), array());
                foreach($params as $param) {
                    foreach($param as $key => $value) {
                        $merged[$key][] = $value;
                    }
                }
                
                $fileParams[$name] = $merged;
            }
        }
        
        $this->method($this->messages, 'sapiUploadedFile', function($data) use($fileParamsFlat, $uploadedFiles) {
            foreach($fileParamsFlat as $key => $fileData) {
                if($fileData === $data) {
                    return $uploadedFiles[$key];
                }
            }
        });
        
        return $fileParams;
    }
    
    protected function getFileData($suffix)
    {
        $fileFields = array(
            'name',
            'type',
            'tmp_name',
            'error',
            'size'
        );
        
        $fileData = array();
        foreach($fileFields as $field) {
            $fileData[$field] = $field.$suffix;
        }
        
        return $fileData;
    }
    
    /**
     * @covers ::getRequestTarget
     * @covers ::<protected>
     */
    public function testGetRequestTarget()
    {
        $this->getRequestTargetTest(true, false);
        $this->getRequestTargetTest(true, true);
    }
    
    protected function message()
    {
        return new \PHPixie\HTTP\Messages\Message\Request\ServerRequest\SAPI(
            $this->messages,
            $this->serverParams,
            $this->queryParams,
            $this->parsedBody,
            $this->cookieParams,
            $this->getFileParams(),
            $this->attributes
        );
    }    
}