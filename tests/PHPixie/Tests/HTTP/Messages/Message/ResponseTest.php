<?php

namespace PHPixie\Tests\HTTP\Messages\Message;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages\Message\Response
 */
class ResponseTest extends ImplementationTest
{
    protected $statusCode   = 200;
    protected $reasonPhrase = 'Pixie';
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstructNoReasonPhrase()
    {
        $this->reasonPhrase = null;
        $message = $this->message();
        $this->assertInstance($message, array(
            'getReasonPhrase' => 'OK'
        ), false);
    }
    
    /**
     * @covers ::getStatusCode
     * @covers ::getReasonPhrase
     * @covers ::withStatus
     * @covers ::<protected>
     */
    public function testWithStatus()
    {
        $sets = array(
            array(404, null, 'Not Found'),
            array(410, 'Bye'),
            array(499, null),
            array(800, 'exception'),
        );
        
        foreach($sets as $set) {
            
            if($set[1] === 'exception') {
                $message = $this->message;
                $this->assertException(function() use($message, $set) {
                    $message->withStatus($set[0]);
                }, '\InvalidArgumentException');
                
            }else{

                if($set[1] !== null) {
                    $new = $this->message->withStatus($set[0], $set[1]);
                }else{
                    $new = $this->message->withStatus($set[0]);
                }

                $this->assertInstance($new, array(
                    'getStatusCode'   => $set[0],
                    'getReasonPhrase' => isset($set[2]) ? $set[2] : $set[1]
                ));
            }
        }
    }
                          
    
    protected function getMethodMap()
    {
        return array_merge(
            parent::getMethodMap(),
            array(
                'getStatusCode'   => $this->statusCode,
                'getReasonPhrase' => $this->reasonPhrase
            )
        );
    }
    
    protected function message()
    {
        return new \PHPixie\HTTP\Messages\Message\Response(
            $this->protocolVersion,
            $this->headers,
            $this->body,
            $this->statusCode,
            $this->reasonPhrase
        );
    }
}