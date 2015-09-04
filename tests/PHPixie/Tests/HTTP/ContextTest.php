<?php

namespace PHPixie\Tests\HTTP;

/**
 * @coversDefaultClass PHPixie\HTTP\Context
 */
class ContextTest extends \PHPixie\Test\Testcase
{
    protected $request;
    protected $cookies;
    protected $server;
    
    protected $context;
    
    public function setUp()
    {
        $this->request = $this->quickMock('\PHPixie\HTTP\Request');
        $this->cookies = $this->quickMock('\PHPixie\HTTP\Context\Cookies');
        $this->session = $this->quickMock('\PHPixie\HTTP\Context\Session');
        
        $this->context = new \PHPixie\HTTP\Context(
            $this->request,
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
     * @covers ::request
     * @covers ::<protected>
     */
    public function testRequest()
    {
        $this->assertSame($this->request, $this->context->request());
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