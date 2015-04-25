<?php

namespace PHPixie\Tests\HTTP;

/**
 * @coversDefaultClass PHPixie\HTTP\Output
 */
class OutputTest extends \PHPixie\Test\Testcase
{
    protected $output;
    protected $file;
    
    public function setUp()
    {
        $this->file = tempnam(sys_get_temp_dir(), 'http_output_test');
        $this->output = $this->getMock(
            '\PHPixie\HTTP\Output',
            array(
                'header',
                'setCookie',
                'output',
                'fpassthru'
            )
        );
    }
    
    public function tearDown()
    {
        if(file_exists($this->file)) {
            unlink($this->file);
        }
    }
    
    /**
     * @covers ::responseMessage
     * @covers ::<protected>
     */
    public function testResponseMessage()
    {
        $this->responseMessageTest('1.0', 'string');
        $this->responseMessageTest(null, 'file');
    }
    
    /**
     * @covers ::response
     * @covers ::<protected>
     */
    public function testResponse()
    {
        $response = $this->quickMock('\PHPixie\HTTP\Responses\Response');
        $context  = $this->quickMock('\PHPixie\HTTP\Context');
        
        $responseMessage = $this->prepareResponseMessage('1.0', 'string');
        $this->method($response, 'asResponseMessage', $responseMessage, array($context), 0);
        $this->output->response($response, $context);
        
        $responseMessage = $this->prepareResponseMessage('1.0', 'string');
        $this->method($response, 'asResponseMessage', $responseMessage, array(), 0);
        $this->output->response($response);
    }
    
    /**
     * @runInSeparateProcess
     * @covers ::<protected>
     */
    public function testMethods()
    {
        ob_start();
        $this->output = new \PHPixie\HTTP\Output();
        $this->methodsTest('string');
        $this->methodsTest('file');
        ob_end_clean();
    }
    
    protected function responseMessageTest($protocolVersion, $bodyType)
    {
        $responseMessage = $this->prepareResponseMessage($protocolVersion, $bodyType);
        $this->output->responseMessage($responseMessage);
    }
    
    protected function prepareResponseMessage($protocolVersion, $bodyType)
    {
        $at = 0;
        $this->prepareStatusHeader(200, 'OK', $protocolVersion, $at);
        $headers = $this->prepareHeaders($at);
        $body = $this->prepareBody($bodyType, $at);
        
        return $this->responseMessage($protocolVersion, 200, 'OK', $headers, $body);   
    }
    
    protected function methodsTest($bodyType)
    {
        $headers = array(
            'Pixie' => array('fairy')
        );
        
        if($bodyType === 'file') {
            $handle = fopen($this->file, 'r');
            $body = $this->prepareResourceStream($handle);
            
        }else{
            $body = $this->preparePsrStream('string', 'test');
        }
        
        $responseMessage = $this->responseMessage('1.1', 200, 'OK', $headers, $body);
        $this->output->responseMessage($responseMessage);
    }
    
    protected function prepareStatusHeader($statusCode, $reasonPhrase, $protocolVersion, &$at)
    {
        if($protocolVersion === null) {
            $protocolVersion = '1.1';
        }
        
        $this->prepareMethod('header', array("HTTP/{$protocolVersion} $statusCode $reasonPhrase"), $at);
    }
    
    protected function prepareHeaders(&$at)
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
        
        foreach($headers as $name => $lines) {
            foreach($lines as $line) {
                $this->prepareMethod('header', array("$name: $line", false), $at);
            }
        }
        
        return $headers;
    }
    
    protected function prepareBody($type, &$at)
    {
        if($type === 'file') {
            $handle = fopen($this->file, 'r');
            $body = $this->prepareResourceStream($handle);
            $this->prepareMethod('fpassthru', array($handle), $at);
            
        }else{
            $body = $this->preparePsrStream('test');
            $this->prepareMethod('output', array('test'), $at);
        }
        
        return $body;
    }
    
    protected function prepareResourceStream($handle)
    {
        $stream = $this->getStream();
        file_put_contents('test', $this->file);
        
        $this->method($stream, 'rewind', null, array(), 0);
        $this->method($stream, 'resource', $handle, array(), 1);
        
        return $stream;
    }
    
    protected function preparePsrStream($string)
    {
        $stream = $this->getPsrStream();
        $this->method($stream, '__toString', $string, array(), 0);
        return $stream;
    }
    
    protected function prepareMethod($method, $params, &$at)
    {
        $this->method($this->output, $method, null, $params, $at++);
    }
    
    protected function responseMessage($protocolVersion, $statusCode, $reasonPhrase, $headerArray, $body)
    {
        $response = $this->quickMock('\Psr\Http\Message\ResponseInterface');
        $methods = array(
            'getProtocolVersion' => $protocolVersion,
            'getStatusCode'      => $statusCode,
            'getReasonPhrase'    => $reasonPhrase,
            'getHeaders'         => $headerArray,
            'getBody'            => $body,
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
    
    protected function getPsrStream()
    {
        return $this->quickMock('\Psr\Http\Message\StreamInterface');
    }
    
    protected function getStream()
    {
        return $this->quickMock('\PHPixie\HTTP\Messages\Stream\Implementation');
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