<?php

namespace PHPixie\Tests\HTTP\Messages\URI;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages\URI\SAPI
 */
class SAPITest extends \PHPixie\Tests\HTTP\Messages\URITest
{
    protected $fragment = '';
    protected $userInfo = '';
    
    protected $server;
    
    public function setUp()
    {
        $this->server = array(
            'HTTP_HOST'    => $this->host,
            'REQUEST_URI'  => $this->path,
            'QUERY_STRING' => '?' . $this->query
        );
        
        parent::setUp();
    }
    
    /**
     * @covers ::requireScheme
     */
    public function testRequireScheme()
    {
        $this->server['HTTPS'] = 'off';
        $this->assertInstance($this->uri(), array(), false);
        
        $this->scheme = 'https';
        $this->server['HTTPS'] = 'on';
        $this->assertInstance($this->uri(), array(), false);
    }
    
    /**
     * @covers ::requireHostAndPort
     */
    public function testRequireHostAndPort()
    {
        $this->port = 555;
        $this->server['HTTP_HOST'].= ':555';
        $this->assertInstance($this->uri(), array(), false);
    }
    
    /**
     * @covers ::requirePath
     */
    public function testRequirePath()
    {
        $this->server['REQUEST_URI'].= '?test';
        $this->assertInstance($this->uri(), array(), false);
    }

    
    protected function uri()
    {
        return new \PHPixie\HTTP\Messages\URI\SAPI($this->server);
    }
}