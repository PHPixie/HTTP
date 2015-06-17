<?php

namespace PHPixie\Tests\HTTP\Data;

/**
 * @coversDefaultClass PHPixie\HTTP\Data\Server
 */
class ServerTest extends \PHPixie\Test\Testcase
{
    protected $serverArray  = array(
        'FAIRY' => 'Trixie',
        'PIXIE' => 'Blum'
    );
    
    protected $server;
    
    public function setUp()
    {
        $this->server = new \PHPixie\HTTP\Data\Server($this->serverArray);
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
        $this->assertSame('Trixie', $this->server->get('fairy'));
        
        $this->assertSame(null, $this->server->get('trixie'));
        $this->assertSame('test', $this->server->get('trixie', 'test'));
    }
    
    /**
     * @covers ::getRequired
     * @covers ::<protected>
     */
    public function testGetRequired()
    {
        $this->assertSame('Trixie', $this->server->getRequired('fairy'));
        
        $server = $this->server;
        $this->assertException(function() use($server) {
            $server->getRequired('trixie');
        }, '\PHPixie\HTTP\Exception');
    }
    
    /**
     * @covers ::asArray
     * @covers ::<protected>
     */
    public function testAsArray()
    {
        $this->assertSame($this->serverArray, $this->server->asArray());
    }
}