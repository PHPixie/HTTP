<?php

namespace PHPixie\Tests\HTTP\Responses;

/**
 * @coversDefaultClass PHPixie\HTTP\Responses\Response
 */
class ResponseTest extends \PHPixie\Test\Testcase
{
    protected $messages;
    protected $headers;
    protected $body;
    protected $statusCode = 302;
    protected $reasonPhrase = 'test';
    
    protected $response;
    
    protected $defaultStatusCode = 200;
    
    public function setUp()
    {
        $this->messages = $this->quickMock('\PHPixie\HTTP\Messages');
        $this->headers = $this->quickMock('\PHPixie\HTTP\Data\Headers');
        $this->body = $this->quickMock('\Psr\Http\Message\StreamInterface');
        
        $this->response = $this->response();
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
        
    }
    
    /**
     * @covers ::headers
     * @covers ::<protected>
     */
    public function testHeaders()
    {
        $this->assertSame($this->headers, $this->response->headers());
    }
    
    /**
     * @covers ::body
     * @covers ::<protected>
     */
    public function testBody()
    {
        $this->assertSame($this->body, $this->response->body());
    }
    
    /**
     * @covers ::statusCode
     * @covers ::reasonPhrase
     * @covers ::setStatus
     * @covers ::<protected>
     */
    public function testStatus()
    {   
        $this->assertStatus($this->statusCode, $this->reasonPhrase);
        
        $this->response->setStatus(301, 'Redirect');
        $this->assertStatus(301, 'Redirect');
        
        $this->response->setStatus(404);
        $this->assertStatus(404, null);
        
        $this->response = new \PHPixie\HTTP\Responses\Response(
            $this->messages,
            $this->headers,
            $this->body
        );
        $this->assertStatus($this->defaultStatusCode, null);
    }
    
    /**
     * @covers ::asResponseMessage
     * @covers ::<protected>
     */
    public function testAsResponseMessage()
    {
        $this->asResponseMessageTest(false);
        $this->asResponseMessageTest(true, false);
        $this->asResponseMessageTest(true, true, false);
        $this->asResponseMessageTest(true, true, true);
    }
    
    protected function asResponseMessageTest($contextExists, $cookiesExist = true, $withHeaderCookies = false)
    {
        $headers = array(
            'Pixie' => array('Trixie')
        );
        
        $cookieHeaders = array('Stella', 'Blum');
        $expectedHeaders = $headers;
        
        if($contextExists) {
            
            $cookieUpdates = array();
            
            if($cookiesExist) {
                if($withHeaderCookies) {
                    $headers['set-cookie'] = array('Fairy');
                    $expectedHeaders['set-cookie'] = array('Fairy', 'Stella', 'Blum');
                    
                }else{
                    $expectedHeaders['Set-Cookie'] = array('Stella', 'Blum');
                }
                
                foreach($cookieHeaders as $header) {
                    $update = $this->quickMock('\PHPixie\HTTP\Context\Cookies\Update');
                    $this->method($update, 'asHeader', $header, array(), 0);
                    $cookieUpdates[]= $update;
                }    
            }

            $context = $this->quickMock('\PHPixie\HTTP\Context');
            $cookies = $this->quickMock('\PHPixie\HTTP\Context\Cookies');
            $this->method($context, 'cookies', $cookies, array(), 0);
            $this->method($cookies, 'updates', $cookieUpdates, array(), 0);
            
        }else{
            $context = null;
        }
        
        $this->method($this->headers, 'asArray', $headers, array(), 0);
        
        $responseMessage = $this->quickMock('\Psr\Http\Message\ResponseInterface');
        $this->method($this->messages, 'response', $responseMessage, array(
            '1.1',
            $expectedHeaders,
            $this->body,
            $this->statusCode,
            $this->reasonPhrase
        ), 0);
        
        if($context !== null) {
            $result = $this->response->asResponseMessage($context);
        }else{
            $result = $this->response->asResponseMessage();
        }
        
        $this->assertSame($responseMessage, $result);
    }
    
    protected function prepareMergeHeaders($context) {
    
    }
    
    protected function assertStatus($code, $reasonPhrase)
    {
        $this->assertSame($code, $this->response->statusCode());
        $this->assertSame($reasonPhrase, $this->response->reasonPhrase());
    }
    
    public function response()
    {
        return new \PHPixie\HTTP\Responses\Response(
            $this->messages,
            $this->headers,
            $this->body,
            $this->statusCode,
            $this->reasonPhrase
        );
    }
}