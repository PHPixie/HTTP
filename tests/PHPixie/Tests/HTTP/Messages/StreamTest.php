<?php

namespace PHPixie\Tests\HTTP\Messages;

abstract class StreamTest extends \PHPixie\Test\Testcase
{
    protected $stream;
    
    /**
     * @covers ::<public>
     * @covers ::<protected>
     */
    public function testDetached()
    {
        $this->stream->detach();
        
        $sets = array(
            array('getSize', null),
            array('tell', 'exception'),
            array('eof', true),
            array('isSeekable', false),
            array('isReadable', false),
            array('isWritable', false),
            array('seek', array(1), 'exception'),
            array('rewind', 'exception'),
            array('write', array('a'), 'exception'),
            array('read', array(1), 'exception'),
            array('getContents', 'exception'),
            array('getMetadata', array()),
            array('getMetadata', array('a'), null),
            array('__toString', '')
        );
        
        foreach($sets as $set) {
            if(array_key_exists(2, $set)) {
                $params = $set[1];
                $expect = $set[2];
            }else{
                $params = array();
                $expect = $set[1];
            }
            
            $callback = array($this->stream, $set[0]);
            
            if($expect === 'exception') {
                $this->assertException(function() use($callback, $params) {
                    call_user_func_array($callback, $params);
                }, '\RuntimeException');
            }else{
                $this->assertSame($expect, call_user_func_array($callback, $params));
            }
        }
    }
}