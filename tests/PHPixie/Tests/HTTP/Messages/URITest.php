<?php

namespace PHPixie\Tests\HTTP\Messages;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages\URI
 */
abstract class URITest extends \PHPixie\Test\Testcase
{
    protected $scheme    = 'http';
    protected $userInfo  = '';
    protected $host      = 'localhost';
    protected $port      = null;
    protected $path      = '/pixie';
    protected $query     = 'a=1';
    protected $fragment  = 'b=2';
    
    protected $ports = array(
        'http'  => 80,
        'https' => 443
    );
    
    protected $uri;
    
    public function setUp()
    {
        $this->uri = $this->uri();
    }
    
    /**
     * @covers ::<public>
     * @covers ::<protected>
     */
    public function testConstruct()
    {
        $this->assertInstance($this->uri, array(), false);
    }
    
    /**
     * @covers ::__toString
     * @covers ::getAuthority
     * @covers ::<protected>
     * @covers \PHPixie\HTTP\Messages\URI::<protected>
     */
    public function testToString()
    {
        $string = $this->getToString();
        
        for($i=0; $i<2; $i++) {
            $this->assertSame($string, (string) $this->uri);
        }
    }
    
    /**
     * @covers ::getAuthority
     * @covers ::__toString
     * @covers ::withHost
     * @covers ::<protected>
     */
    public function testWithHost()
    {
        $this->withTest('host', array(
            'null' => '',
            ''     => '',
            'test' => 'test'
        ));
    }
    
    /**
     * @covers ::getPort
     * @covers ::getAuthority
     * @covers ::__toString
     * @covers ::withPort
     * @covers ::<protected>
     */
    public function testWithPort()
    {
        $this->withTest('port', array(
            'null'  => null,
            43      => 43,
            'test'  => 'exception',
            0       => 'exception',
            65536   => 'exception',
            $this->ports[$this->scheme] => null
        ));
        
        $this->scheme = 'https';
        $this->uri = $this->uri->withScheme('https');
        $this->withTest('port', array(
            443 => null
        ));
    }
    
    /**
     * @covers ::withQuery
     * @covers ::__toString
     * @covers ::<protected>
     */
    public function testWithQuery()
    {
        $this->withTest('query', array(
            'c=3'    => 'c=3',
            '?c=3'   => 'c=3',
            'c=a b'  => 'c=a%20b',
            '?c=3#'  => 'exception'
        ));
    }
    
    /**
     * @covers ::getAuthority
     * @covers ::__toString
     * @covers ::withFragment
     * @covers ::<protected>
     */
    public function testWithFragment()
    {
        $this->withTest('fragment', array(
            'c=3'  => 'c=3',
            '#c=3' => 'c=3',
            'c=a b' => 'c=a%20b',
        ));
    }
    
    /**
     * @covers ::withScheme
     * @covers ::__toString
     * @covers ::<protected>
     */
    public function testWithScheme()
    {
        $this->withTest('scheme', array(
            'null'  => '',
            ''      => '',
            'hTTp'  => 'http',
            'https' => 'https',
            'test'  => 'exception'
        ));
    }
    
    /**
     * @covers ::getAuthority
     * @covers ::__toString
     * @covers ::withPath
     * @covers ::<protected>
     */
    public function testWithPath()
    {
        $this->withTest('path', array(
            null    => '/',
            ''      => '/',
            '/test' => '/test',
            'test'  => '/test',
            'test?' => 'exception',
            'test#' => 'exception'
        ));
    }
    
    /**
     * @covers ::getAuthority
     * @covers ::__toString
     * @covers ::withUserInfo
     * @covers ::<protected>
     */
    public function testWithUserInfo()
    {
        $sets = array(
            array(null, ''),
            array('', ''),
            array('pixie', 'pixie'),
            array('pixie', 'trixie', 'pixie:trixie'),
            array('', 'trixie', '')
        );
        
        $uri = $this->uri;
        
        foreach($sets as $set) {
            
            if(count($set) === 2) {
                $new = $this->uri->withUserInfo($set[0]);
                
            }else{
                $new = $this->uri->withUserInfo($set[0], $set[1]);
            }
            
            $this->userInfo = end($set);
            $this->assertInstance($new, array(
                'getUserInfo'  => $this->userInfo,
                'getAuthority' => $this->getAuthority()
            ));
        }
    }
    
    protected function withTest($name, $sets)
    {
        $uri = $this->uri;
        
        foreach($sets as $value => $expected) {
            $method = 'with'.ucfirst($name);
            
            if($value === 'null') {
                $value = null;
            }
            
            if($expected === 'exception') {
                $this->assertException(function() use($uri, $method, $value){
                    $uri->$method($value);
                }, '\InvalidArgumentException');
                
            }else{
                $this->$name = $expected;
                $new = $this->uri->$method($value);
                $this->assertInstance($new, array(
                    'get'.ucfirst($name) => $expected
                ));
            }
        }
    }
    
    protected function getToString()
    {
        $string = '';
        
        if($this->scheme !== '') {
            $string.= $this->scheme.'://';
        }

        $string.= $this->getAuthority();
        $string.= $this->path;
        
        if($this->query !== '') {
            $string.= '?'.$this->query;
        }
        
        if($this->fragment !== '') {
            $string.= '#'.$this->fragment;
        }
        
        return $string;
    }
    
    protected function getAuthority()
    {
        $authority = (string) $this->host;
        
        if($this->userInfo !== '') {
            $authority = $this->userInfo.'@'.$authority;
        }
        
        if($this->port !== null) {
            if($this->port !== $this->ports[$this->scheme]) {
                $authority.= ':'.$this->port;
            }
        }
        
        return $authority;
    }
    
    protected function assertInstance($instance, $overrides = array(), $assertNotSame = true)
    {
        if($assertNotSame) {
            $this->assertNotSame($this->uri, $instance);
        }
        
        $methods = array_merge($this->getMethodMap(), $overrides);
        
        foreach($methods as $method => $value) {
            $this->assertSame($value, $instance->$method());
        }
    }
    
    protected function getMethodMap()
    {
        return array(
            'getScheme'    => $this->scheme,
            'getUserInfo'  => $this->userInfo,
            'getHost'      => $this->host,
            'getPort'      => $this->port,
            'getPath'      => $this->path,
            'getQuery'     => $this->query,
            'getFragment'  => $this->fragment,
            'getAuthority' => $this->getAuthority(),
            '__toString'   => $this->getToString(),
        );
    }
    
    abstract protected function uri();
}