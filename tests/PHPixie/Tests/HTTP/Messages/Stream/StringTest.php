<?php

namespace PHPixie\Tests\HTTP\Messages\Streamable;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages\Streamable\String
 */
class StringTest extends \PHPixie\Test\Testcase
{
    protected $string = 'pixie';
    protected $stream;
    
    public function setUp()
    {
        $this->stream = new \PHPixie\HTTP\Messages\Streamable\String($this->string);
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
        $stream = new \PHPixie\HTTP\Messages\Streamable\String();
        $this->assertSame('', (string) $stream);
    }
    
    /**
     * @covers ::__toString
     * @vovers ::<protected>
     */
    public function testToString()
    {
        $this->assertSame($this->string, (string) $this->stream);
    }
    
    /**
     * @covers ::write
     * @vovers ::<protected>
     */
    public function testWrite()
    {
        $this->stream->write('Trixie');
        $this->assertSame('pixieTrixie', (string) $this->stream);
    }
    
    /**
     * @covers ::detach
     * @covers ::<protected>
     */
    public function testDetach()
    {
        $this->assertSame(null, $this->stream->detach());
        $this->assertSame('', (string) $this->stream);
    }
    
    /**
     * @covers ::close
     * @covers ::<protected>
     */
    public function testClose()
    {
        $this->stream->close();
        $this->assertSame('', (string) $this->stream);
    }
    
    /**
     * @covers ::<public>
     * @covers ::<protected>
     */
    public function testMethods()
    {
        $sets = array(
            array('getSize', strlen($this->string)),
            array('tell', false),
            array('eof', true),
            array('isSeekable', false),
            array('isWritable', true),
            array('isReadable', true),
            array('seek', array('a'), false),
            array('rewind', false),
            array('read', array(1), ''),
            array('getContents', ''),
            array('getMetadata', array()),
            array('getMetadata', array('a'), null),
        );
        
        foreach($sets as $set) {
            if(array_key_exists(2, $set)) {
                $params = $set[1];
                $expect = $set[2];
            }else{
                $params = array();
                $expect = $set[1];
            }
            
            $callback = array($this->stream, $set[0]);
            $this->assertSame($expect, call_user_func_array($callback, $params));
        }
    }
    
}