<?php

namespace PHPixie\Tests\HTTP\Messages\Message;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages\Message\Request
 */
abstract class RequestTest extends \PHPixie\Tests\HTTP\Messages\MessageTest
{
    protected $method = 'Get';
    
    public function setUp()
    {
        $this->uri = $this->getUri();
        parent::setUp();
    }
    
    /**
     * @covers ::getUri
     * @covers ::withUri
     * @covers ::<protected>
     */
    public function testUri()
    {
        $uri = $this->getUri();
        $new = $this->message->withUri($uri);
        
        $this->assertInstance($new, array(
            'getUri' => $uri
        ));
    }
    
    /**
     * @covers ::getMethod
     * @covers ::withMethod
     * @covers ::<protected>
     */
    public function testMethod()
    {
        $method = 'Post';
        $new = $this->message->withMethod($method);
        
        $this->assertInstance($new, array(
            'getMethod' => $method
        ));
    }
    
    /**
     * @covers ::getRequestTarget
     * @covers ::<protected>
     */
    public function testGetRequestTarget()
    {
        $this->getRequestTargetTest(false, false);
        $this->getRequestTargetTest(true, false);
        $this->getRequestTargetTest(true, true);
    }
    
    protected function getRequestTargetTest($withPath, $withQuery)
    {
        
        if($withPath) {
            $this->uri = null;
            $expected = '/';
            
        }else{
            $path = '/pixie';
            $expected = $path;
            
            if($withQuery) {
                $query = 'test=1';
                $expected.= '?'.$query;
            }else{
                $query = null;
            }
            
            $this->method($this->uri, 'getPath', $path, array(), 0);
            $this->method($this->uri, 'getQuery', $query, array(), 1);
        }
        $this->message = $this->message();
        
        for($i=0; $i<1; $i++) {
            $this->assertSame($expected, $this->message->getRequestTarget());
        }
    }
    
    /**
     * @covers ::withRequestTarget
     * @covers ::<protected>
     */
    public function testWithRequestTarget()
    {
        $requestTarget = '/pixie';
        $new = $this->message->withRequestTarget($requestTarget);
        
        $this->assertInstance($new, array(
            'getRequestTarget' => $requestTarget
        ));
        
        $this->setExpectedException('\InvalidArgumentException');
        $this->message->withRequestTarget(' ');
    }
    
    /**
     * @covers ::withMethod
     * @covers ::<protected>
     */
    public function testInvalidMethod()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->message->withMethod('Invalid');
    }
    
    protected function getMethodMap()
    {
        return array_merge(
            parent::getMethodMap(),
            array(
                'getMethod' => $this->method,
                'getUri'    => $this->uri
            )
        );
    }
    
    protected function getUri()
    {
        return $this->abstractMock('\Psr\Http\Message\UriInterface');
    }
}