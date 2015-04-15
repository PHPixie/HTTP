<?php

namespace PHPixie\Tests\HTTP\Context;

/**
 * @coversDefaultClass PHPixie\HTTP\Context\Cookies
 */
class CookiesTest extends \PHPixie\Test\Testcase
{
    protected $builder;
    protected $cookieArray = array(
        'pixie' => 'Trixie',
        'fairy' => 'Blum'
    );
    
    protected $cookies;
    
    public function setUp()
    {
        $this->builder = $this->quickMock('\PHPixie\HTTP\Builder');
        $this->cookies = new \PHPixie\HTTP\Context\Cookies($this->cookieArray);
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
        $this->assertSame('Trixie', $this->cookies->get('pixie'));
        
        $this->assertSame(null, $this->cookies->get('trixie'));
        $this->assertSame(5, $this->cookies->get('trixie', 5));
    }
    
    /**
     * @covers ::getRequired
     * @covers ::<protected>
     */
    public function testGetRequired()
    {
        $this->assertSame('Trixie', $this->cookies->getRequired('pixie'));
        
        $cookies = $this->cookies;
        $this->assertException(function(){});
        $this->assertSame(5, $this->cookies->get('trixie', 5));
    }

}