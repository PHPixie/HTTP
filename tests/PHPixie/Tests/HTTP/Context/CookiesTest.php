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
        
        $this->cookies = new \PHPixie\HTTP\Context\Cookies(
            $this->builder,
            $this->cookieArray
        );
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
        foreach($this->cookieArray as $name => $value) {
            $this->assertSame($value, $this->cookies->get($name));
        }
        
        
        $this->assertSame(null, $this->cookies->get('trixie'));
        $this->assertSame(5, $this->cookies->get('trixie', 5));
    }
    
    /**
     * @covers ::exists
     * @covers ::<protected>
     */
    public function testExists()
    {
        $this->assertSame(true, $this->cookies->exists('pixie'));
        $this->assertSame(false, $this->cookies->exists('trixie'));
    }
    
    /**
     * @covers ::getRequired
     * @covers ::<protected>
     */
    public function testGetRequired()
    {
        foreach($this->cookieArray as $name => $value) {
            $this->assertSame($value, $this->cookies->getRequired($name));
        }
        
        $cookies = $this->cookies;
        $this->assertException(function() use($cookies){
            $cookies->getRequired('trixie');
        }, '\PHPixie\HTTP\Exception');
    }
    
    /**
     * @covers ::set
     * @covers ::asArray
     * @covers ::updates
     * @covers ::<protected>
     */
    public function testSet()
    {
        $cookies = $this->cookieArray;
        $updates = array();
        
        $cookies['trixie'] = 7;
        $updates['trixie'] = $this->prepareCookieUpdate('trixie', 7);
        
        $this->cookies->set('trixie', 7);
        $this->assertCookies($cookies, $updates);
        
        $cookies['trixie'] = 8;
        $updates['trixie'] = $this->prepareCookieUpdate('trixie', 8, time()+5, '/trixie', 'fairies', true, true);
        
        $this->cookies->set('trixie', 8, 5, '/trixie', 'fairies', true, true);
        $this->assertCookies($cookies, $updates);
        
        unset($cookies['trixie']);
        $updates['trixie'] = $this->prepareCookieUpdate('trixie', 8, time() - 5);
        
        $this->cookies->set('trixie', 8, -5);
        $this->assertCookies($cookies, $updates);
    }
    
    /**
     * @covers ::remove
     * @covers ::asArray
     * @covers ::updates
     * @covers ::<protected>
     */
    public function testRemove()
    {
        $cookies = $this->cookieArray;
        $updates = array();
        
        unset($cookies['pixie']);
        $updates['pixie'] = $this->prepareCookieUpdate('pixie', null, time() - 3600*24*30);
        
        $this->cookies->remove('pixie');
        $this->assertCookies($cookies, $updates);
    }
    
    protected function prepareCookieUpdate(
        $name,
        $value,
        $expires = null,
        $path = '/',
        $domain = null,
        $secure = false,
        $httpOnly = false
    )
    {
        $update = $this->quickMock('\PHPixie\HTTP\Context\Cookies\Update');
        $this->method($this->builder, 'cookiesUpdate', $update, func_get_args(), 0);
        return $update;
    }
    
    protected function assertCookies($cookies, $updates)
    {
        $this->assertSame($cookies, $this->cookies->asArray());
        $this->assertSame(array_values($updates), $this->cookies->updates());
    }

}