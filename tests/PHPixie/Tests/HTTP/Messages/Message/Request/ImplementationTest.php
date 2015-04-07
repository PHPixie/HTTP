<?php

namespace PHPixie\Tests\HTTP\Messages\Message\Request;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages\Message\Request\Implementation
 */
class ImplementationTest extends \PHPixie\Tests\HTTP\Messages\Message\RequestTest
{
    protected $method = 'Get';
    protected $uri;
    
    public function setUp()
    {
        $this->uri = $this->getUri();
        parent::setUp();
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testInvalidHeaders()
    {
        $this->headers['Fairy'] = array();
        $this->setExpectedException('\InvalidArgumentException');
        $this->message();
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstructInvalidMethod()
    {
        $this->method = 'INVALID';
        $this->setExpectedException('\InvalidArgumentException');
        $this->message();
    }
    
    protected function getUri()
    {
        return $this->abstractMock('\Psr\Http\Message\UriInterface');
    }
    
    public function message()
    {
        return new \PHPixie\HTTP\Messages\Message\Request\Implementation(
            $this->protocolVersion,
            $this->headers,
            $this->body,
            $this->method,
            $this->uri
        );
    }

}