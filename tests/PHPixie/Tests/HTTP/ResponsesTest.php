<?php

namespace PHPixie\Tests\HTTP;

/**
 * @coversDefaultClass PHPixie\HTTP\Responses
 */
class ResponsesTest extends \PHPixie\Test\Testcase
{
    protected $builder;
    
    protected $responses;
    
    protected $messages;
    
    public function setUp()
    {
        $this->builder = $this->quickMock('\PHPixie\HTTP\Builder');
        
        $this->responses = $this->getMock(
            '\PHPixie\HTTP\Responses',
            array('buildResponse'),
            array($this->builder)
        );
        
        $this->messages = $this->quickMock('\PHPixie\HTTP\Messages');
        $this->method($this->builder, 'messages', $this->messages, array());
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
    
    }
    
    /**
     * @covers ::string
     * @covers ::<protected>
     */
    public function testString()
    {
        $response = $this->prepareStringResponse('test');
        $this->assertSame($response, $this->responses->string('test'));
    }
    
    /**
     * @covers ::redirect
     * @covers ::<protected>
     */
    public function testRedirect()
    {
        $url = 'pixie';
        
        $response = $this->prepareStringResponse('', array('Location' => $url), 302);
        $this->assertSame($response, $this->responses->redirect($url));
        
        $response = $this->prepareStringResponse('', array('Location' => $url), 301);
        $this->assertSame($response, $this->responses->redirect($url, 301));
    }
    
    /**
     * @covers ::json
     * @covers ::<protected>
     */
    public function testJson()
    {
        $data = array('a' => 1);
        $string = json_encode($data, JSON_UNESCAPED_UNICODE);
        
        $response = $this->prepareStringResponse(
            $string,
            array(
                'Cache-Control' => 'no-cache, must-revalidate',
                'Expires'       => 'Mon, 26 Jul 1997 05:00:00 GMT',
                'Content-type'  => 'application/json'
            )
        );
        $this->assertSame($response, $this->responses->json($data));
    }
    
    /**
     * @covers ::streamFile
     * @covers ::<protected>
     */
    public function teststreamFile()
    {
        $body = $this->getStream();
        $this->method($this->messages, 'stream', $body, array('fairy.png'), 0);
        
        $response = $this->prepareResponse($body);
        $this->assertSame($response, $this->responses->streamFile('fairy.png'));
    }
    
    /**
     * @covers ::download
     * @covers ::<protected>
     */
    public function testDownload()
    {
        $body = $this->getStringStream();
        $this->method($this->messages, 'stringStream', $body, array('test'), 0);
        
        $response = $this->prepareDownloadResponse('pixie.png', 'image/png', $body);
        $this->assertSame($response, $this->responses->download('pixie.png', 'image/png', 'test'));
    }
    
    /**
     * @covers ::downloadFile
     * @covers ::<protected>
     */
    public function testDownloadFile()
    {
        $body = $this->getStream();
        $this->method($this->messages, 'stream', $body, array('fairy.png'), 0);
        
        $response = $this->prepareDownloadResponse('pixie.png', 'image/png', $body);
        $this->assertSame($response, $this->responses->downloadFile('pixie.png', 'image/png', 'fairy.png'));
    }
    
    /**
     * @covers ::response
     * @covers ::<protected>
     */
    public function testResponse()
    {
        $body = $this->getStream();
        
        $response = $this->prepareResponse($body, array(), 200, null, 0);
        $this->assertSame($response, $this->responses->response($body));
        
        $response = $this->prepareResponse($body, array('a' => 1), 302, 'test', 0);
        $this->assertSame($response, $this->responses->response($body, array('a' => 1), 302, 'test'));
    }
    
    /**
     * @covers ::buildResponse
     * @covers ::<protected>
     */
    public function testBuildResponse()
    {
        $responses = new \PHPixie\HTTP\Responses($this->builder);
        
        $body = $this->getStream();
        
        $headers = $this->quickMock('\PHPixie\HTTP\Data\Headers\Editable');
        $this->method($this->builder, 'editableHeaders', $headers, array(array()), 0);
        
        $response = $responses->response($body);
        $this->assertInstance($response, '\PHPixie\HTTP\Responses\Response', array(
            'messages' => $this->messages,
            'headers'  => $headers,
            'body'     => $body
        ));
    }
    
    protected function prepareStringResponse($string, $headerArray = array(), $statusCode = 200)
    {
        $body = $this->getStringStream();
        $this->method($this->messages, 'stringStream', $body, array($string), 0);
        
        return $this->prepareResponse($body, $headerArray, $statusCode);
    }
    
    protected function prepareDownloadResponse($fileName, $contentType, $body, $builderAt = 1)
    {
        $headers = array(
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"'
        );
        
        return $this->prepareResponse($body, $headers);
    }

    protected function prepareResponse($body, $headerArray = array(), $statusCode = 200, $reasonPhrase = null, $builderAt = 1)
    {
        $headers = $this->quickMock('\PHPixie\HTTP\Data\Headers\Editable');
        $this->method($this->builder, 'editableHeaders', $headers, array($headerArray), $builderAt);
        
        $response = $this->quickMock('\PHPixie\HTTP\Responses\Response');
        $this->method($this->responses, 'buildResponse', $response, array($headers, $body, $statusCode, $reasonPhrase), 0);
        return $response;
    }
    
    protected function getStringStream()
    {
        return $this->quickMock('\PHPixie\HTTP\Messages\Stream\StringStream');
    }
    
    protected function getStream()
    {
        return $this->quickMock('\PHPixie\HTTP\Messages\Stream\Implementation');
    }
}
