<?php

namespace PHPixie\Tests\HTTP\Data;

/**
 * @coversDefaultClass PHPixie\HTTP\Data\Headers
 */
class HeadersTest extends \PHPixie\Test\Testcase
{
    protected $headerArray  = array(
        'Fairy' => array(
            'Pixie'
        ),
        'Pixie-Name'  => array(
            'Trixie',
            'Blum'
        )
    );
    
    protected $headers;
    
    public function setUp()
    {
        $this->headers = $this->headers();
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
    
    }
    
    /**
     * @covers ::get
     * @covers ::<protected>
     */
    public function testGet()
    {
        $this->assertSame('Pixie', $this->headers->get('fairy'));
        $this->assertSame('Trixie,Blum', $this->headers->get('Pixie-Name'));
        
        $this->assertSame('', $this->headers->get('trixie'));
        $this->assertSame('test', $this->headers->get('trixie', 'test'));
    }
    
    /**
     * @covers ::getRequired
     * @covers ::<protected>
     */
    public function testGetRequired()
    {
        $this->assertSame('Pixie', $this->headers->getRequired('fairy'));
        $this->assertSame('Trixie,Blum', $this->headers->getRequired('pixie-name'));
        
        $headers = $this->headers;
        $this->assertException(function() use($headers) {
            $headers->getRequired('trixie');
        }, '\PHPixie\HTTP\Exception');
    }
    
    /**
     * @covers ::getLines
     * @covers ::<protected>
     */
    public function testGetLines()
    {
        $this->assertSame($this->headerArray['Fairy'], $this->headers->getLines('fairy'));
        $this->assertSame($this->headerArray['Pixie-Name'], $this->headers->getLines('pixie-name'));
        
        $this->assertSame(array(), $this->headers->getLines('trixie'));
        $this->assertSame('test', $this->headers->getLines('trixie', 'test'));
    }
    
    /**
     * @covers ::getRequiredLines
     * @covers ::<protected>
     */
    public function testGetRequiredLines()
    {
        $this->assertSame($this->headerArray['Fairy'], $this->headers->getRequiredLines('fairy'));
        $this->assertSame($this->headerArray['Pixie-Name'], $this->headers->getRequiredLines('pixie-name'));
        
        $headers = $this->headers;
        $this->assertException(function() use($headers) {
            $headers->getRequiredLines('trixie');
        }, '\PHPixie\HTTP\Exception');
    }
    
    /**
     * @covers ::asArray
     * @covers ::<protected>
     */
    public function testAsArray()
    {
        $this->assertHeaders($this->headerArray);
    }
    
    protected function assertHeaders($headers)
    {
        $this->assertSame($headers, $this->headers->asArray());
    }
    
    protected function headers()
    {
        return new \PHPixie\HTTP\Data\Headers($this->headerArray);
    }
}