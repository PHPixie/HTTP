<?php

namespace PHPixie\Tests\HTTP\Context\Session;

class SAPIStub extends \PHPixie\HTTP\Context\Session\SAPI
{
    protected $sessionArray;
    protected $sessionStarted = false;
    
    public function __construct(&$sessionArray)
    {
        $this->sessionArray = &$sessionArray;
    }
    
    public function isSessionStarted()
    {
        return $this->sessionStarted;
    }

    protected function &session()
    {
        return $this->sessionArray;
    }
    
    protected function sessionStart()
    {
        $this->sessionStarted = true;
    }
}

/**
 * @coversDefaultClass PHPixie\HTTP\Context\Session\SAPI
 */
class SAPITest extends \PHPixie\Test\Testcase
{
    protected $sessionArray = array(
        'pixie' => 'Trixie',
        'fairy' => 'Blum'
    );
    
    protected $session;
    
    public function setUp()
    {
        $this->session = $this->getMock(
            '\PHPixie\Tests\HTTP\Context\Session\SAPIStub',
            array('sessionId'),
            array(&$this->sessionArray)
        );
    }
    
    /**
     * @covers ::get
     * @covers ::<protected>
     */
    public function testGet()
    {
        foreach($this->sessionArray as $name => $value) {
            $this->assertSame($value, $this->session->get($name));
        }
        $this->assertSessionStarted();
        
        $this->assertSame(null, $this->session->get('trixie'));
        $this->assertSame(5, $this->session->get('trixie', 5));
    }
    
    /**
     * @covers ::exists
     * @covers ::<protected>
     */
    public function testExists()
    {
        $this->assertSame(true, $this->session->exists('pixie'));
        $this->assertSessionStarted();
        
        $this->assertSame(false, $this->session->exists('trixie'));
    }
    
    /**
     * @covers ::getRequired
     * @covers ::<protected>
     */
    public function testGetRequired()
    {
        foreach($this->sessionArray as $name => $value) {
            $this->assertSame($value, $this->session->getRequired($name));
        }
        $this->assertSessionStarted();
        
        $session = &$this->session;
        $this->assertException(function() use($session) {
            $session->getRequired('trixie');
        }, '\PHPixie\HTTP\Exception');
    }
    
    /**
     * @covers ::set
     * @covers ::<protected>
     */
    public function testSet()
    {
        $expected = $this->sessionArray;
        $expected['trixie'] = 5;
        
        $this->session->set('trixie', 5);
        $this->assertSame($expected, $this->sessionArray);
        $this->assertSessionStarted();
    }
    
    /**
     * @covers ::remove
     * @covers ::<protected>
     */
    public function testRemove()
    {
        $expected = $this->sessionArray;
        unset($expected['pixie']);
        
        $this->session->remove('pixie');
        $this->assertSame($expected, $this->sessionArray);
        $this->assertSessionStarted();
    }
    
    /**
     * @covers ::asArray
     * @covers ::<protected>
     */
    public function testAsArray()
    {
        $this->assertSame($this->sessionArray, $this->session->asArray());
        $this->assertSessionStarted();
    }
    
    /**
     * @covers ::setId
     * @covers ::<protected>
     */
    public function testSetId()
    {
        $this->assertSame(true, $this->session->exists('pixie'));
        $this->assertSessionStarted();
        
        $this->method($this->session, 'sessionId', null, array(5), 'once');
        $this->session->setId(5);
        
        $this->assertSame(false, $this->session->isSessionStarted());
    }
    
    /**
     * @covers ::id
     * @covers ::<protected>
     */
    public function testId()
    {
        $this->method($this->session, 'sessionId', 5, array(), 'once');
        
        $this->assertSame(5, $this->session->id());
        $this->assertSessionStarted();
    }
    
    /**
     * @covers ::<protected>
     * @runInSeparateProcess
     */
    public function testMethods()
    {
        $this->session = new \PHPixie\HTTP\Context\Session\SAPI();
        
        ob_start();
        $this->session->setId(1);
        $this->session->id(1);
        session_destroy();
        
        ob_end_clean();
    }
    
    protected function assertSessionStarted()
    {
        $this->assertSame(true, $this->session->isSessionStarted());
    }
    
}