<?php

namespace PHPixie\Tests\HTTP;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages
 */
class MessagesTest extends \PHPixie\Test\Testcase
{
    protected $messages;
    
    public function setUp()
    {
        $this->messages = new \PHPixie\HTTP\Messages();
    }
    
    /**
     * @covers ::message
     * @covers ::<protected>
     */
    public function testMessage()
    {
        $this->instanceTest(
            'message',
            '\PHPixie\HTTP\Messages\Message\Implementation',
            $this->getMessageParams()
        );
    }
    
    /**
     * @covers ::request
     * @covers ::<protected>
     */
    public function testRequest()
    {
        $this->instanceTest(
            'request',
            '\PHPixie\HTTP\Messages\Message\Request\Implementation',
            $this->getRequestParams()
        );
    }
    
    /**
     * @covers ::serverRequest
     * @covers ::<protected>
     */
    public function testServerRequest()
    {
        $params = array_merge(
            $this->getRequestParams(),
            $this->getArrayParams(array(
                'serverParams',
                'queryParams',
                'parsedBody',
                'cookieParams',
                'uploadedFiles',
                'attributes'
            ))
        );
        
        $this->instanceTest(
            'serverRequest',
            '\PHPixie\HTTP\Messages\Message\Request\ServerRequest\Implementation',
            $params,
            array(
                'attributes' => array()
            )
        );
    }
    
    /**
     * @covers ::sapiServerRequest
     * @covers ::<protected>
     */
    public function testSapiServerRequest()
    {
        $params = $this->getArrayParams(array(
            'serverParams',
            'queryParams',
            'parsedBody',
            'cookieParams',
            'fileParams',
            'attributes'
        ));
        
        $params['serverParams']['REQUEST_METHOD'] = 'GET';

        $this->instanceTest(
            'sapiServerRequest',
            '\PHPixie\HTTP\Messages\Message\Request\ServerRequest\SAPI',
            $params,
            array(
                'serverParams' => $_SERVER = array('REQUEST_METHOD' => 'GET'),
                'queryParams'  => $_GET    = array('get'    => 1),
                'parsedBody'   => $_POST   = array('post'   => 1),
                'cookieParams' => $_COOKIE = array('cookie' => 1),
                'fileParams'   => $_FILES  = array('files'  => 1),
                'attributes'   => array()
            ),
            array(
                'messages' => $this->messages,
                'method'   => 'GET'
            )
        );
    }
    
    /**
     * @covers ::response
     * @covers ::<protected>
     */
    public function testResponse()
    {
        $this->instanceTest(
            'response',
            '\PHPixie\HTTP\Messages\Message\Response',
            $this->getResponseParams(),
            array(
                'statusCode'   => 200,
                'reasonPhrase' => 'OK',
            )
        );
    }
    
    /**
     * @covers ::stream
     * @covers ::<protected>
     */
    public function testStream()
    {
        $this->instanceTest(
            'stream',
            '\PHPixie\HTTP\Messages\Stream\Implementation',
            array(
                'uri'  => 'fairy.png',
                'mode' => 'w'
            ),
            array(
                'mode' => 'r'
            )
        );
    }
    
    /**
     * @covers ::stringStream
     * @covers ::<protected>
     */
    public function testStringStream()
    {
        $stream = $this->messages->stringStream('test');
        $this->assertInstance(
            $stream,
            '\PHPixie\HTTP\Messages\Stream\StringStream',
            array(
                'string' => 'test'
            )
        );
        
        $stream = $this->messages->stringStream();
        $this->assertInstance(
            $stream,
            '\PHPixie\HTTP\Messages\Stream\StringStream',
            array(
                'string' => ''
            )
        );
    }
    
    /**
     * @covers ::uri
     * @covers ::<protected>
     */
    public function testUri()
    {
        $uri = $this->messages->uri('http://phpixie.com');
        $this->assertInstance($uri, '\PHPixie\HTTP\Messages\URI\Implementation', array(
            'parts' => array(
                'scheme' => 'http',
                'host'   => 'phpixie.com'
            )
        ));
    }
    
    /**
     * @covers ::sapiUri
     * @covers ::<protected>
     */
    public function testSapiUri()
    {
        $server = array('a' => 1);
        
        $this->instanceTest(
            'sapiUri',
            '\PHPixie\HTTP\Messages\URI\SAPI',
            array(
                'server'  => array('a' => 1),
            ),
            array(
                'server'  => $_SERVER = array('server' => 1),
            )
        );
    }
    
    /**
     * @covers ::uploadedFile
     * @covers ::<protected>
     */
    public function testUploadedFile()
    {
        $this->instanceTest(
            'uploadedFile',
            '\PHPixie\HTTP\Messages\UploadedFile\Implementation',
            array(
                'file'            => 'pixie.png',
                'clientFilename'  => 'fairy',
                'clientMediaType' => 'image/png',
                'size'            => 300,
                'error'           => 1,
            ),
            array(
                'clientFilename'  => null,
                'clientMediaType' => null,
                'size'            => null,
                'error'           => null,
            ),
            array(
                'messages' => $this->messages,
            )
        );
    }
    
    
    /**
     * @covers ::sapiUploadedFile
     * @covers ::<protected>
     */
    public function testSapiUploadedFile()
    {
        $fileData = array(
            'name'     => 'fairy',
            'type'     => 'image/png',
            'tmp_name' => 'pixie.png',
            'error'    => 1,
            'size'     => 300
        );
        
        $this->assertInstance(
            $this->messages->sapiUploadedFile($fileData),
            '\PHPixie\HTTP\Messages\UploadedFile\SAPI',
            array(
                'messages' => $this->messages,
                'file'            => 'pixie.png',
                'clientFilename'  => 'fairy',
                'clientMediaType' => 'image/png',
                'size'            => 300,
                'error'           => 1,
            )
        );
    }
    
    protected function getArrayParams($names)
    {
        $params = array();
        foreach($names as $name) {
            $params[$name] = array($name => 1);
        }
        
        return $params;
    }
    
    protected function getRequestParams()
    {
        return array_merge(
            $this->getMessageParams(),
            array(
                'method' => 'GET',
                'uri'    => $this->getUri()
            )
        );
    }
    
    protected function getResponseParams()
    {
        return array_merge(
            $this->getMessageParams(),
            array(
                'statusCode'   => '301',
                'reasonPhrase' => 'test'
            )
        );
    }
    
    protected function getMessageParams()
    {
        return array(
            'protocolVersion' => '1.1',
            'headers' => array('a' => 1),
            'body'    => $this->getStream()
        );
    }
    
    protected function instanceTest($method, $class, $params, $defaults = array(), $overrides = array())
    {
        $propertyMap = array_merge($params, $overrides);
        $instance = call_user_func_array(array($this->messages, $method), $params);
        $this->assertInstance($instance, $class, $propertyMap);
        
        if(!empty($defaults)) {
            $shortParams = $params;
            foreach($defaults as $key => $value) {
                unset($shortParams[$key]);
                $params[$key] = $value;
            }
            
            $propertyMap = array_merge($params, $overrides);
            $instance = call_user_func_array(array($this->messages, $method), $params);
            $this->assertInstance($instance, $class, $propertyMap);
        }
    }
    
    protected function getStream()
    {
        return $this->quickMock('\Psr\Http\Message\StreamInterface');
    }
    
    protected function getUri()
    {
        return $this->quickMock('\Psr\Http\Message\UriInterface');
    }
}