<?php

namespace PHPixie\Tests\HTTP;

/**
 * @coversDefaultClass PHPixie\HTTP\Request
 */
class RequestTest extends \PHPixie\Test\Testcase
{
    protected $builder;
    protected $serverRequest;
    
    protected $request;
    
    protected $dataMethods = array(

    );
    
    public function setUp()
    {
        $this->builder = $this->quickMock('\PHPixie\HTTP\Builder');
        $this->serverRequest = $this->quickMock('Psr\Http\Message\ServerRequestInterface');
        
        $this->request = new \PHPixie\HTTP\Request($this->builder, $this->serverRequest);
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
    
    }
    
    /**
     * @covers ::query
     * @covers ::data
     * @covers ::attributes
     * @covers ::uploads
     * @covers ::<protected>
     */
    public function testData()
    {
        $dataMethods = array(
            'query'      => 'getQueryParams',
            'data'       => 'getParsedBody',
            'attributes' => 'getAttributes',
            'uploads'    => 'getUploadedFiles',
        );
        
        foreach($dataMethods as $method => $serverMethod) {
            $data = $this->abstractMock('\PHPixie\Slice\Data');
            $this->prepareData($serverMethod, 'data', $data);
            for($i=0; $i<2; $i++) {
                $this->assertSame($data, $this->request->$method());
            }
        }
    }
    
    /**
     * @covers ::server
     * @covers ::<protected>
     */
    public function testServer()
    {
        $serverData = $this->quickMock('\PHPixie\HTTP\Data\Server');
        $this->prepareData('getServerParams', 'serverData', $serverData);
        for($i=0; $i<2; $i++) {
            $this->assertSame($serverData, $this->request->server());
        }
    }
    
    /**
     * @covers ::headers
     * @covers ::<protected>
     */
    public function testHeaders()
    {
        $headers = $this->quickMock('\PHPixie\HTTP\Data\Headers');
        $this->prepareData('getHeaders', 'headers', $headers);
        for($i=0; $i<2; $i++) {
            $this->assertSame($headers, $this->request->headers());
        }
    }
    
    /**
     * @covers ::serverRequest
     * @covers ::<protected>
     */
    public function testServerRequest()
    {
        $this->assertSame($this->serverRequest, $this->request->serverRequest());
    }
    
    /**
     * @covers ::method
     * @covers ::<protected>
     */
    public function testMethod()
    {
        $this->method($this->serverRequest, 'getMethod', 'GET', array(), 0);
        $this->assertSame('GET', $this->request->method());
    }
    
    /**
     * @covers ::uri
     * @covers ::<protected>
     */
    public function testUri()
    {
        $uri = $this->quickMock('\Psr\Http\Message\UriInterface');
        $this->method($this->serverRequest, 'getUri', $uri, array(), 0);
        $this->assertSame($uri, $this->request->uri());
    }

    protected function prepareData($serverRequestMethod, $builderMethod, $instance)
    {
        $array = array('test' => 1);
        
        $this->method($this->serverRequest, $serverRequestMethod, $array, array(), 0);
        $this->method($this->builder, $builderMethod, $instance, array($array), 0);
    }
}