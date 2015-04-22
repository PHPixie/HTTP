<?php

namespace PHPixie\Tests\HTTP\Context\Cookies;

/**
 * @coversDefaultClass PHPixie\HTTP\Context\Cookies\Update
 */
class UpdateTest extends \PHPixie\Test\Testcase
{
    protected $name     = 'pixie';
    protected $value    = 'Trixie';
    protected $expires  = 5;
    protected $path     = '/fairy';
    protected $domain   = 'fairies';
    protected $secure   = true;
    protected $httpOnly = true;
    
    protected $update;
    
    public function setUp()
    {
        $this->update = new \PHPixie\HTTP\Context\Cookies\Update(
            $this->name,
            $this->value,
            $this->expires,
            $this->path,
            $this->domain,
            $this->secure,
            $this->httpOnly
        );
    }
    
    /**
     * @covers ::<public>
     * @covers ::<protected>
     */
    public function testMethods()
    {
        $this->methodsTest();
        
        $this->expires  = null;
        $this->path     = '/';
        $this->domain   = null;
        $this->secure   = false;
        $this->httpOnly = false;
        $this->update = new \PHPixie\HTTP\Context\Cookies\Update(
            $this->name,
            $this->value
        );
        
        $this->methodsTest();
    }
    
    protected function methodsTest()
    {
        $methods = array(
            'name',
            'value',
            'expires',
            'path',
            'domain',
            'secure',
            'httpOnly'
        );
        
        foreach($methods as $method) {
            $this->assertSame($this->$method, $this->update->$method());
        }
    }
}