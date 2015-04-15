<?php

namespace PHPixie\Tests\HTTP\Messages\Message\Request;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages\Message\Request\ServerRequest
 */
abstract class ServerRequestTest extends \PHPixie\Tests\HTTP\Messages\Message\RequestTest
{
    protected $serverParams  = array();
    protected $queryParams   = array();
    protected $parsedBody    = array();
    protected $cookieParams  = array();
    protected $uploadedFiles = array();
    
    protected $parameterNames = array(
        'serverParams',
        'queryParams',
        'parsedBody',
        'cookieParams',
    );
    
    protected $attributes = array(
        'pixie' => 'Trixie',
        'fairy' => 'Blum',
    );
    
    public function setUp()
    {
        foreach($this->parameterNames as $name) {            
            $array = &$this->$name;
            $array[$name] = 'Pixie';
        }
        
        $this->uploadedFiles = array(
            'pixie' => $this->getUploadedFile(),
            'fairy' => array(
                $this->getUploadedFile(),
                $this->getUploadedFile()
            )
        );
        
        parent::setUp();
    }
    
    /**
     * @covers ::withQueryParams
     * @covers ::withParsedBody
     * @covers ::withCookieParams
     * @covers ::<protected>
     */
    public function testWithParams()
    {
        $params = array(
            'queryParams',
            'parsedBody',
            'cookieParams',
            'uploadedFiles'
        );
        
        foreach($params as $name) {
            if($name === 'uploadedFiles') {
                $value = $this->getUploadedFile();
                
            }else{
                $value = 'Pixie';
            }
            
            $value = array('test' => $value);
            $method = 'with'.ucfirst($name);
            $new = $this->message->$method($value);        
            $this->assertInstance($new, array(
                'get'.ucfirst($name) => $value
            ));
        }
    }

    /**
     * @covers ::getAttribute
     * @covers ::<protected>
     */
    public function testGetAttribute()
    {
        foreach($this->attributes as $name => $value) {
            $this->assertSame($value, $this->message->getAttribute($name));
        }
        
        $this->assertSame(null, $this->message->getAttribute('missing'));
        $this->assertSame(5, $this->message->getAttribute('missing', 5));
    }
    
    /**
     * @covers ::withAttribute
     * @covers ::<protected>
     */
    public function testWithAttribute()
    {
        $new = $this->message->withAttribute('test', 'Stella');
        
        $attributes = $this->attributes;
        $attributes['test'] = 'Stella';
        
        $this->assertInstance($new, array(
            'getAttributes' => $attributes
        ));
    }
    
    /**
     * @covers ::withUploadedFiles
     * @covers ::<protected>
     */
    public function testWithUploadedFiles()
    {
        $uploadedFiles = array('pixie' => $this->getUploadedFile());
        $new = $this->message->withUploadedFiles($uploadedFiles);
        $this->assertInstance($new, array(
            'getUploadedFiles' => $uploadedFiles
        ));
    }
    
    /**
     * @covers ::withoutAttribute
     * @covers ::<protected>
     */
    public function testWithoutAttribute()
    {
        $new = $this->message->withoutAttribute('pixie');
        
        $attributes = $this->attributes;
        unset($attributes['pixie']);
        
        $this->assertInstance($new, array(
            'getAttributes' => $attributes
        ));
    }
    
    protected function getMethodMap()
    {
        $methodMap = parent::getMethodMap();
        foreach($this->parameterNames as $name) {
            $methodMap['get'.ucfirst($name)] = $this->$name;
        }
        
        $methodMap['getUploadedFiles'] = $this->uploadedFiles;
        $methodMap['getAttributes'] = $this->attributes;
        return $methodMap;
    }
    
    protected function getUploadedFile()
    {
        return $this->abstractMock('\Psr\Http\Message\UploadedFileInterface');
    }
}