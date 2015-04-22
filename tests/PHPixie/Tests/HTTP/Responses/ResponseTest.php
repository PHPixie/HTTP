<?php

namespace PHPixie\Tests\HTTP\Responses;

/**
 * @coversDefaultClass PHPixie\HTTP\Responses\Response
 */
class ResponseTest extends \PHPixie\Test\Testcase
{
    protected $headers;
    protected $body;
    
    protected $response;
    
    protected $defaultStatusCode = 200;
    
    public function setUp()
    {
        $this->headers = $this->quickMock('\PHPixie\HTTP\Headers');
        $this->body = $this->quickMock('\Psr\Http\Message\StreamInterface');
        
        $this->respone = $this->response();
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
        $this->assertStatus($this->defaultStatus, null);
        
        $this->response->setStatus(301, 'Redirect');
        $this->response->assertStatus(301, 'Redirect');
        
        $this->response->setStatus(404);
        $this->response->assertStatus(404, null);
    }
    
    protected function assertStatus($code, $reasonPhrase)
    {
        $this->assertSame($code, $this->response->statusCode());
        $this->assertSame($reasonPhrase, $this->response->reasonPhrase());
    }
    
    public function response()
    {
        return new \PHPixie\HTTP\Responses\Response(
            $this->headers,
            $this->body
        );
    }
}