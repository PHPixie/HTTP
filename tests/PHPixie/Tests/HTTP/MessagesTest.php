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
        $params = $this->getMessageParams();
        
        $message = call_user_func_array(array($this->messages, 'message'), $params);
        $this->assertInstance(
            $message,
            '\PHPixie\HTTP\Messages\Message\Implementation',
            $params
        );
    }
    
    /**
     * @covers ::request
     * @covers ::<protected>
     */
    public function testRequest()
    {
        $params = $this->getRequestParams();
        
        $message = call_user_func_array(array($this->messages, 'request'), $params);
        $this->assertInstance(
            $message,
            '\PHPixie\HTTP\Messages\Message\Request\Implementation',
            $params
        );
    }
    
    /**
     * @covers ::serverRequest
     * @covers ::<protected>
     */
    public function testServerRequest()
    {
        $params = $this->getServerRequestParams();
        
        $message = call_user_func_array(array($this->messages, 'serverRequest'), $params);
        $this->assertInstance(
            $message,
            '\PHPixie\HTTP\Messages\Message\Request\ServerRequest\Implementation',
            $params
        );
        
        $shortParams = $params;
        unset($shortParams['attributes']);
        $params['attributes'] = array();
        
        $message = call_user_func_array(array($this->messages, 'serverRequest'), $shortParams);
        $this->assertInstance(
            $message,
            '\PHPixie\HTTP\Messages\Message\Request\ServerRequest\Implementation',
            $params
        );
    }
    
    /**
     * @covers ::response
     * @covers ::<protected>
     */
    public function testResponse()
    {
        $params = $this->getResponseParams();
        
        $message = call_user_func_array(array($this->messages, 'response'), $params);
        $this->assertInstance(
            $message,
            '\PHPixie\HTTP\Messages\Message\Response',
            $params
        );
        
        $shortParams = $params;
        unset($shortParams['statusCode']);
        unset($shortParams['reasonPhrase']);
        $params['statusCode']   = 200;
        $params['reasonPhrase'] = 'OK';
        
        $message = call_user_func_array(array($this->messages, 'response'), $shortParams);
        $this->assertInstance(
            $message,
            '\PHPixie\HTTP\Messages\Message\Response',
            $params
        );
    }
    
    /**
     * @covers ::stream
     * @covers ::<protected>
     */
    public function testStream()
    {
        $uri = 'fairy.png';
        
        $stream = $this->messages->stream($uri, 'w');
        $this->assertInstance(
            $stream,
            '\PHPixie\HTTP\Messages\Stream\Implementation',
            array(
                'uri' => $uri,
                'mode' => 'w'
            )
        );
        
        $stream = $this->messages->stream($uri);
        $this->assertInstance(
            $stream,
            '\PHPixie\HTTP\Messages\Stream\Implementation',
            array(
                'uri' => $uri,
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
            '\PHPixie\HTTP\Messages\Stream\String',
            array(
                'string' => 'test'
            )
        );
        
        $stream = $this->messages->stringStream();
        $this->assertInstance(
            $stream,
            '\PHPixie\HTTP\Messages\Stream\String',
            array(
                'string' => ''
            )
        );
    }
    
    protected function getServerRequestParams()
    {
        $params = $this->getRequestParams();
        
        $names = array(
            'serverParams',
            'queryParams',
            'parsedBody',
            'cookieParams',
            'uploadedFiles',
            'attributes'
        );
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
    
    protected function getStream()
    {
        return $this->quickMock('\Psr\Http\Message\StreamInterface');
    }
    
    protected function getUri()
    {
        return $this->quickMock('\Psr\Http\Message\UriInterface');
    }
}