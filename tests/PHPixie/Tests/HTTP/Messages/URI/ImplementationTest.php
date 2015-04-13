<?php

namespace PHPixie\Tests\HTTP\Messages\URI;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages\URI\Implementation
 */
class ImplementationTest extends \PHPixie\Tests\HTTP\Messages\URITest
{
    protected $sourceUri;
    
    public function setUp()
    {
        $this->sourceUri = $this->getToString();
        parent::setUp();
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstructWithPort()
    {
        $this->port = 445;
        $this->sourceUri = $this->getToString();
        $this->assertInstance($this->uri(), array(), false);
    }
    
    protected function uri()
    {
        return new \PHPixie\HTTP\Messages\URI\Implementation($this->sourceUri);
    }
}