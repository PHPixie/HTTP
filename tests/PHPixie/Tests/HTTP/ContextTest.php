<?php

namespace PHPixie\Tests\HTTP;

/**
 * @coversDefaultClass PHPixie\HTTP\Context
 */
class ContextTest extends \PHPixie\Test\Testcase
{
    protected $serverRequest;
    protected $cookies;
    protected $server;
    
    protected $context;
    
    public function setUp()
    {
        $this->serverRequest = $this->quickMock('\Psr\Http\Message\ServerRequestInterface');
        $this->cookies       = $this->quickMock('\PHPixie\HTTP\Context\Cookies');
        $this->session       = $this->quickMock('\PHPixie\HTTP\Context\Session');
        
        $this->context = new \PHPixie\HTTP\Context(
            $this->serverRequest,
            $this->cookies,
            $this->session
        );
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
        
    }
    
    /**
     * @covers ::serverRequest
     * @covers ::<protected>
     */
    public function testServerRequest()
    {
        $this->assertSame($this->serverRequest, $this->context->serverRequest());
    }
    
    /**
     * @covers ::cookies
     * @covers ::<protected>
     */
    public function testCookies()
    {
        $this->assertSame($this->cookies, $this->context->cookies());
    }
    
    /**
     * @covers ::session
     * @covers ::<protected>
     */
    public function testSession()
    {
        $this->assertSame($this->session, $this->context->session());
    }

}