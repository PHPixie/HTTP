<?php

namespace PHPixie\Tests;

/**
 * @coversDefaultClass \PHPixie\HTTP
 */
class HTTPTest extends \PHPixie\Test\Testcase
{
    protected $slice;
    
    protected $http;
    
    protected $builder;
    
    public function setUp()
    {
        $this->slice = $this->quickMock('\PHPixie\Slice');
        
        $this->http = $this->getMockBuilder('\PHPixie\HTTP')
            ->setMethods(array('buildBuilder'))
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->builder = $this->quickMock('\PHPixie\HTTP\Builder');
        $this->method($this->http, 'buildBuilder', $this->builder, array(
            $this->slice
        ), 0);
        
        $this->http->__construct(
            $this->slice
        );
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstructor()
    {
        
    }
    
    /**
     * @covers ::buildBuilder
     * @covers ::<protected>
     */
    public function testBuildBuilder()
    {
        $this->http = new \PHPixie\HTTP(
            $this->slice
        );
        
        $builder = $this->http->builder();
        $this->assertInstance($builder, '\PHPixie\HTTP\Builder', array(
            'slice' => $this->slice
        ));
    }
    
    /**
     * @covers ::builder
     * @covers ::<protected>
     */
    public function testBuilder()
    {
        $this->assertSame($this->builder, $this->http->builder());
    }
    
    /**
     * @covers ::messages
     * @covers ::<protected>
     */
    public function testMessages()
    {
        $messages = $this->prepareBuilderMethod('messages');
        $this->assertSame($messages, $this->http->messages());
    }
    
    /**
     * @covers ::responses
     * @covers ::<protected>
     */
    public function testResponses()
    {
        $responses = $this->prepareBuilderMethod('responses');
        $this->assertSame($responses, $this->http->responses());
    }
    
    /**
     * @covers ::sapiServerRequest
     * @covers ::<protected>
     */
    public function testSapiServerRequest()
    {
        $serverRequest = $this->prepareSapiServerRequest();
        $this->assertSame($serverRequest, $this->http->sapiServerRequest());
    }
    
    /**
     * @covers ::request
     * @covers ::<protected>
     */
    public function testRequest()
    {
        $serverRequest = $this->getServerRequest();
        $request = $this->getRequest();
        
        $this->method($this->builder, 'request', $request, array($serverRequest), 0);
        $this->assertSame($request, $this->http->request($serverRequest));
        
        $serverRequest = $this->prepareSapiServerRequest();
        $this->method($this->builder, 'request', $request, array($serverRequest), 1);
        $this->assertSame($request, $this->http->request());
    }
    
    /**
     * @covers ::output
     * @covers ::<protected>
     */
    public function testOutput()
    {
        $response = $this->quickMock('\PHPixie\HTTP\Responses\Response');
        $context  = $this->getContext();
        
        $output = $this->prepareBuilderMethod('output', null);
        $this->method($output, 'response', null, array($response, $context), 0);
        $this->http->output($response, $context);
        
        $this->method($output, 'response', null, array($response, null), 0);
        $this->http->output($response);
    }
    
    /**
     * @covers ::outputResponseMessage
     * @covers ::<protected>
     */
    public function testOutputResponseMessage()
    {
        $responseMessage = $this->quickMock('\PHPixie\HTTP\Messages\Message\Response');
        
        $output = $this->prepareBuilderMethod('output');
        $this->method($output, 'responseMessage', null, array($responseMessage), 0);
        $this->http->outputResponseMessage($responseMessage);
    }
    
    /**
     * @covers ::context
     * @covers ::<protected>
     */
    public function testContext()
    {
        $request = $this->getRequest();
        $session = $this->getSession();
        
        $context = $this->prepareContext($request, $session);
        $this->assertSame($context, $this->http->context($request, $session));
        
        $context = $this->prepareContext($request);
        $this->assertSame($context, $this->http->context($request));
    }
    
    protected function prepareSapiServerRequest()
    {
        $serverRequest = $this->getServerRequest();
        $messages = $this->prepareBuilderMethod('messages');
        $this->method($messages, 'sapiServerRequest', $serverRequest, array(), 0);
        return $serverRequest;
    }
    
    protected function prepareContext($request, $session = null, $serverRequest = null)
    {
        if($serverRequest === null) {
            $serverRequest = $this->getServerRequest();
        }
        
        $this->method($request, 'serverRequest', $serverRequest, array(), 0);
        
        $cookieArray = array('a' => 1);
        $this->method($serverRequest, 'getCookieParams', $cookieArray, array(), 0);
        
        $at = 0;
        
        $cookies = $this->quickMock('\PHPixie\HTTP\Context\Cookies');
        $this->method($this->builder, 'cookies', $cookies, array($cookieArray), $at++);
        
        if($session == null) {
            $session = $this->quickMock('\PHPixie\HTTP\Context\Session\SAPI');
            $this->method($this->builder, 'sapiSession', $session, array(), $at++);
        }
        
        $context = $this->getContext();
        $this->method($this->builder, 'context', $context, array($request, $cookies, $session), $at++);
        
        return $context;
    }
    
    /**
     * @covers ::contextContainer
     * @covers ::<protected>
     */
    public function testContextContainer()
    {
        $context   = $this->quickMock('\PHPixie\HTTP\Context');
        $container = $this->quickMock('\PHPixie\HTTP\Context\Container\Implementation');
        
        $this->method($this->builder, 'contextContainer', $container, array($context), 0);
        $this->assertSame($container, $this->http->contextContainer($context));
    }
    
    protected function prepareBuilderMethod($name, $at = 0)
    {
        $instance = $this->quickMock('\PHPixie\HTTP\\'.ucfirst($name));
        $this->method($this->builder, $name, $instance, array(), $at);
        return $instance;
    }
    
    protected function getRequest()
    {
        return $this->quickMock('\PHPixie\HTTP\Request');
    }
    
    protected function getServerRequest()
    {
        return $this->quickMock('\Psr\Http\Message\ServerRequestInterface');
    }
    
    protected function getContext()
    {
        return $this->quickMock('\PHPixie\HTTP\Context');
    }
    
    protected function getSession()
    {
        return $this->quickMock('\PHPixie\HTTP\Context\Session');
    }
}