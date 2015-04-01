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
        'Name'  => array(
            'Trixie',
            'Blum'
        )
    );
    protected $body;
    
    protected $message;
    
    public function setUp()
    {
        $this->body = $this->getStreamable();
        
        $this->message = $this->message();
    }
    
    /**
     * @covers ::<public>
     * @covers ::<protected>
     */
    public function testConstruct()
    {
        $this->assertInstance($this->message, array(), true);
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
        $body = $this->getStreamable();
        $new = $this->message->withBody($body);
        
        $this->assertInstance($new, array(
            'getBody' => $body
        ));
    }
    
    protected function assertInstance($instance, $overrides = array(), $same = false)
    {
        $this->assertSame($same, $instance === $this->message);
        
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
    
    protected function getStreamable()
    {
        return $this->abstractMock('\Psr\Http\Message\StreamableInterface');
    }
    
    abstract public function message();
}