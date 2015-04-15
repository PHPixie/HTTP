<?php

namespace PHPixie\Tests\HTTP\Messages;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages\Message
 */
abstract class MessageTest extends \PHPixie\Test\Testcase
{
    protected $protocolVersion = '1.1';
    protected $headers  = array(
        'Fairy' => array(
            'Pixie'
        ),
        'Pixie-Name'  => array(
            'Trixie',
            'Blum'
        )
    );
    protected $body;
    
    protected $message;
    
    public function setUp()
    {
        $this->body = $this->getStream();
        
        $this->message = $this->message();
    }
    
    /**
     * @covers ::<public>
     * @covers ::<protected>
     */
    public function testConstruct()
    {
        $this->assertInstance($this->message, array(), false);
    }
    
    /**
     * @covers ::getHeaders
     * @covers ::<protected>
     */
    public function testHeaders()
    {
        for($i=0; $i<2; $i++) {
            $this->assertSame($this->headers, $this->message->getHeaders());
        }
    }
    
    /**
     * @covers ::getBody
     * @covers ::withBody
     * @covers ::<protected>
     */
    public function testBody()
    {
        $body = $this->getStream();
        $new = $this->message->withBody($body);
        
        $this->assertInstance($new, array(
            'getBody' => $body
        ));
    }
    
    /**
     * @covers ::getProtocolVersion
     * @covers ::withProtocolVersion
     * @covers ::<protected>
     */
    public function testProtocolVersion()
    {
        $protocolVersion = '1.0';
        $new = $this->message->withProtocolVersion($protocolVersion);
        
        $this->assertInstance($new, array(
            'getProtocolVersion' => $protocolVersion
        ));
    }
    
    /**
     * @covers ::getHeaders
     * @covers ::hasHeader
     * @covers ::getHeader
     * @covers ::getHeaderLine
     * @covers ::<protected>
     */
    public function testGetHeader()
    {
        $this->assertSame($this->headers, $this->message->getHeaders());
        
        foreach($this->headers as $name => $lines) {
            $this->assertSame(true, $this->message->hasHeader($name));
            $this->assertSame(implode(',', $lines), $this->message->getHeaderLine($name));
            $this->assertSame($lines, $this->message->getHeader($name));
        }
        
        $this->assertSame(false, $this->message->hasHeader('Pixies'));
        $this->assertSame(null, $this->message->getHeaderLine('Pixies'));
        $this->assertSame(array(), $this->message->getHeader('Pixies'));
    }
    
    /**
     * @covers ::withHeader
     * @covers ::<protected>
     */
    public function testWithHeader()
    {
        $new = $this->message->withHeader('fairy', 'Stella');
        
        $headers = $this->headers;
        unset($headers['Fairy']);
        $headers['fairy'] = array('Stella');
        
        $this->assertInstance($new, array(
            'getHeaders' => $headers
        ));
    }
    
    /**
     * @covers ::withAddedHeader
     * @covers ::<protected>
     */
    public function testWithAddedHeader()
    {
        $new = $this->message->withAddedHeader('fairy', 'Stella');
        
        $headers = $this->headers;
        $headers['Fairy'][] = 'Stella';
        
        $this->assertInstance($new, array(
            'getHeaders' => $headers
        ));
    }

    /**
     * @covers ::withoutHeader
     * @covers ::<protected>
     */
    public function testWithoutHeader()
    {
        $new = $this->message->withoutHeader('Missing');
        $this->assertInstance($new);
        
        $new = $this->message->withoutHeader('pixie-Name');
        $headers = $this->headers;
        unset($headers['Pixie-Name']);
        
        $this->assertInstance($new, array(
            'getHeaders' => $headers
        ));
    }
    
    protected function assertInstance($instance, $overrides = array(), $assertNotSame = true)
    {
        if($assertNotSame) {
            $this->assertNotSame($this->message, $instance);
        }
        
        $methods = array_merge($this->getMethodMap(), $overrides);
        
        foreach($methods as $method => $value) {
            $this->assertSame($value, $instance->$method());
        }
    }
    
    protected function getMethodMap()
    {
        return array(
            'getProtocolVersion' => $this->protocolVersion,
            'getHeaders'         => $this->headers,
            'getBody'            => $this->body
        );
    }
    
    protected function getStream()
    {
        return $this->abstractMock('\Psr\Http\Message\StreamInterface');
    }
    
    abstract protected function message();
}