<?php

namespace PHPixie\Tests\HTTP;

/**
 * @coversDefaultClass PHPixie\HTTP\Output
 */
class OutputTest extends \PHPixie\Test\Testcase
{
    protected $output;
    
    public function setUp()
    {
        $this->output = $this->getMock(
            '\PHPixie\HTTP\Output',
            array(),
            array(
                'header',
                'setCookie',
                'fpassthru',
                'echo'
            )
        );
    }
    
    /**
     * @covers ::response
     * @covers ::<protected>
     */
    public function testResponse()
    {
        $headers = array(
            'pixie' => array(
                'Trixie',
                'Blum'
            ),
            'fairy' => array(
                'Stella'
            )
        );
        $body = $this->getStream();
        
        $response = $this->response(
            $headers,
            $body,
            200,
            'OK'
        );
    }
    
    protected function response($headerArray, $body, $statusCode, $reasonPhrase)
    {
        $headers = $this->getHeaders();
        $this->method($headers, 'headerArray', $headerArray, array());
        
        $response = $this->quickMock('\PHPixie\HTTP\Responses\Response');
        $methods = array(
            'headers'      => $headers,
            'body'         => $body,
            'statusCode'   => $statusCode,
            'reasonPhrase' => $reasonPhrase
        );
        foreach($methods as $method => $value) {
            $this->method($response, $method, $value, array());
        }
        
        return $response;
    }
    
    protected function getHeaders()
    {
        return $this->quickMock('\PHPixie\HTTP\Data\Headers');
    }
    
    protected function getStream()
    {
        return $this->quickMock('\Psr\Http\Message\StreamInterface');
    }
    
    protected function getContext()
    {
        return $this->quickMock('\PHPixie\HTTP\Context');
    }
    
    protected function getCookies()
    {
        return $this->quickMock('\PHPixie\HTTP\Context\Cookies');
    }
    
    protected function getCookieUpdate()
    {
        return $this->quickMock('\PHPixie\HTTP\Context\Cookies\Update');
    }

}