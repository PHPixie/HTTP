<?php

namespace PHPixie\Tests\HTTP;

/**
 * @coversDefaultClass PHPixie\HTTP\Builder
 */
class BuilderTest extends \PHPixie\Test\Testcase
{
    protected $slice;
    protected $builder;
    
    public function setUp()
    {
        $this->slice   = $this->quickMock('\PHPixie\Slice');
        $this->builder = new \PHPixie\HTTP\Builder($this->slice);
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
    
    }
    
    /**
     * @covers ::messages
     * @covers ::<protected>
     */
    public function testMessages()
    {
        $this->instanceTest('messages', '\PHPixie\HTTP\Messages');
    }
    
    /**
     * @covers ::responses
     * @covers ::<protected>
     */
    public function testResponses()
    {
        $this->instanceTest('responses', '\PHPixie\HTTP\Responses', array(
            'builder' => $this->builder
        ));
    }
    
    /**
     * @covers ::output
     * @covers ::<protected>
     */
    public function testOutput()
    {
        $this->instanceTest('output', '\PHPixie\HTTP\Output');
    }
    
    /**
     * @covers ::request
     * @covers ::<protected>
     */
    public function testRequest()
    {
        $serverRequest = $this->quickMock('Psr\Http\Message\ServerRequestInterface');
        
        $request = $this->builder->request($serverRequest);
        $this->assertInstance($request, '\PHPixie\HTTP\Request', array(
            'builder'       => $this->builder,
            'serverRequest' => $serverRequest,
        ));
    }
    
    /**
     * @covers ::data
     * @covers ::<protected>
     */
    public function testData()
    {
        $array = array('t' => 1);
        $arrayData = $this->abstractMock('\PHPixie\Slice\Type\ArrayData');
        
        $this->method($this->slice, 'arrayData', $arrayData, array($array), 0);
        $this->assertSame($arrayData, $this->builder->data($array));
        
        $this->method($this->slice, 'arrayData', $arrayData, array(array()), 0);
        $this->assertSame($arrayData, $this->builder->data());
    }
    
    /**
     * @covers ::headers
     * @covers ::<protected>
     */
    public function testHeaders()
    {
        $headerArray = array('t' => array('a'));
        $headers = $this->builder->headers($headerArray);
        $this->assertInstance($headers, '\PHPixie\HTTP\Data\Headers', array(
            'headers' => $headerArray
        ));
        
        $headers = $this->builder->headers();
        $this->assertInstance($headers, '\PHPixie\HTTP\Data\Headers', array(
            'headers' => array()
        ));
    }
    
    /**
     * @covers ::editableHeaders
     * @covers ::<protected>
     */
    public function testEditableHeaders()
    {
        $headerArray = array('t' => array('a'));
        $headers = $this->builder->editableHeaders($headerArray);
        $this->assertInstance($headers, '\PHPixie\HTTP\Data\Headers\Editable', array(
            'headers' => $headerArray
        ));
        
        $headers = $this->builder->editableHeaders();
        $this->assertInstance($headers, '\PHPixie\HTTP\Data\Headers\Editable', array(
            'headers' => array()
        ));

    }
    
    /**
     * @covers ::serverData
     * @covers ::<protected>
     */
    public function testServer()
    {
        $serverArray = array('t' => 1);
        $serverData = $this->builder->serverData($serverArray);
        $this->assertInstance($serverData, '\PHPixie\HTTP\Data\Server', array(
            'server' => $serverArray
        ));
        
        $serverData = $this->builder->serverData();
        $this->assertInstance($serverData, '\PHPixie\HTTP\Data\Server', array(
            'server' => array()
        ));
    }
    
    /**
     * @covers ::context
     * @covers ::<protected>
     */
    public function testContext()
    {
        $request = $this->quickMock('\PHPixie\HTTP\Request');
        $cookies = $this->quickMock('\PHPixie\HTTP\Context\Cookies');
        $session = $this->quickMock('\PHPixie\HTTP\Context\Session');
        
        $context = $this->builder->context($request, $cookies, $session);
        $this->assertInstance($context, '\PHPixie\HTTP\Context', array(
            'request' => $request,
            'cookies' => $cookies,
            'session' => $session,
        ));
    }
    
    /**
     * @covers ::cookies
     * @covers ::<protected>
     */
    public function testCookies()
    {
        $cookieArray = array('t' => 1);
        $cookies = $this->builder->cookies($cookieArray);
        $this->assertInstance($cookies, '\PHPixie\HTTP\Context\Cookies', array(
            'builder' => $this->builder,
            'cookies' => $cookieArray
        ));
        
        $cookies = $this->builder->cookies();
        $this->assertInstance($cookies, '\PHPixie\HTTP\Context\Cookies', array(
            'builder' => $this->builder,
            'cookies' => array()
        ));
    }
    
    /**
     * @covers ::sapiSession
     * @covers ::<protected>
     */
    public function testSapiSession()
    {
        $session = $this->builder->sapiSession();
        $this->assertInstance($session, '\PHPixie\HTTP\Context\Session\SAPI');
    }
    
    /**
     * @covers ::cookiesUpdate
     * @covers ::<protected>
     */
    public function testCookieUpdate()
    {
        $params = array(
            'name'     => 'pixie',
            'value'    => 'Trixie',
            'expires'  => 5,
            'path'     => '/fairy',
            'domain'   => 'fairies',
            'secure'   => true,
            'httpOnly' => true
        );
        
        $cookieUpdate = call_user_func_array(array($this->builder, 'cookiesUpdate'), $params);
        $this->assertInstance($cookieUpdate, '\PHPixie\HTTP\Context\Cookies\Update', $params);
        
        $params = array(
            'name'     => 'pixie',
            'value'    => 'Trixie',
            'expires'  => null,
            'path'     => '/',
            'domain'   => null,
            'secure'   => false,
            'httpOnly' => false
        );
        
        $cookieUpdate = $this->builder->cookiesUpdate('pixie', 'Trixie');
        $this->assertInstance($cookieUpdate, '\PHPixie\HTTP\Context\Cookies\Update', $params);
    }
    
    /**
     * @covers ::contextContainer
     * @covers ::<protected>
     */
    public function testContextContainer()
    {
        $context = $this->quickMock('\PHPixie\HTTP\Context');
        
        $contextContainer = $this->builder->contextContainer($context);
        $this->assertInstance($contextContainer, '\PHPixie\HTTP\Context\Container\Implementation', array(
            'context' => $context
        ));
    }
    
    protected function instanceTest($method, $class, $propertyMap = array())
    {
        $instance = $this->builder->$method();
        $this->assertInstance($instance, $class, $propertyMap);
        $this->assertSame($instance, $this->builder->$method());
    }
}