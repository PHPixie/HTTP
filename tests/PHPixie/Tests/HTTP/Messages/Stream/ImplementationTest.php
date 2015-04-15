<?php

namespace PHPixie\Tests\HTTP\Messages\Stream;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages\Stream\Implementation
 */
class ImplementationTest extends \PHPixie\Tests\HTTP\Messages\StreamTest
{
    protected $file;
    protected $contents = 'test';
    
    public function setUp()
    {
        $this->file = tempnam(sys_get_temp_dir(), 'pixie_stream_test');
        file_put_contents($this->file, $this->contents);
        
        $this->stream = $this->stream();
    }
    
    public function tearDown()
    {
        $this->stream->close();
        if(file_exists($this->file)) {
            unlink($this->file);
        }
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
        
    }
    
    /**
     * @covers ::__toString
     * @covers ::<protected>
     */
    public function testToString()
    {
        $this->assertSame($this->contents, (string) $this->stream);
    }
    
    /**
     * @covers ::detach
     * @covers ::<protected>
     */
    public function testDetach()
    {
        $resource = $this->stream->detach();
        $this->assertSame(true, is_resource($resource));
        fclose($resource);
        
        $this->assertSame(null, $this->stream->detach());
    }
    
    /**
     * @covers ::close
     * @covers ::<protected>
     */
    public function testClose()
    {
        $this->stream->close();
        $this->assertSame(null, $this->stream->detach());
        
        $this->stream->close();
    }
    
    /**
     * @covers ::getSize
     * @covers ::<protected>
     */
    public function testGetSize()
    {
        $size = mb_strlen($this->contents, '8bit');
        $this->assertSame($size, $this->stream->getSize());
    }
    
    /**
     * @covers ::isSeekable
     * @covers ::isReadable
     * @covers ::isWritable
     * @covers ::<protected>
     */
    public function testIsFlags()
    {
        $map = array(
            'r+' => array(
                'isSeekable' => true,
                'isReadable' => true,
                'isWritable' => true
            ),
            'w' => array(
                'isSeekable' => true,
                'isReadable' => false,
                'isWritable' => true
            )
        );
        
        foreach($map as $mode => $flags) {
            $stream = $this->stream($mode);
            foreach($flags as $method => $value) {
                $this->assertSame($value, $stream->$method());
            }
        }
    }
    
    /**
     * @covers ::seek
     * @covers ::tell
     * @covers ::eof
     * @covers ::rewind
     * @covers ::<protected>
     */
    public function testSeek()
    {
        $stream = $this->stream('r');
        
        $this->assertSame(false, $stream->eof());
        $this->assertSame(0, $stream->tell());
        
        $stream->seek(2);
        $this->assertSame(2, $stream->tell());
        
        $stream->seek(0, SEEK_END);
        $this->assertSame(4, $stream->tell());
        
        $stream->read(1);
        
        $this->assertSame(true, $stream->eof());
        
        $stream->rewind();
        $this->assertSame(0, $stream->tell());
        
    }
    
    /**
     * @covers ::read
     * @covers ::getContents
     * @covers ::eof
     * @covers ::<protected>
     */
    public function testRead()
    {
        $this->assertSame($this->contents[0], $this->stream->read(1));
        $this->assertSame(substr($this->contents, 1), $this->stream->getContents());
        $this->assertSame(true, $this->stream->eof());
        
        $this->stream->seek(1);
        $this->assertSame(substr($this->contents, 1, 2), $this->stream->read(2));
        
        $this->assertSame($this->contents, (string) $this->stream);
        $this->assertSame(true, $this->stream->eof());
    }
    
    /**
     * @covers ::write
     * @covers ::<protected>
     */
    public function testWrite()
    {
        $contents = substr_replace($this->contents, 'nm', 1, 2);
        $this->stream->seek(1);
        
        $this->assertSame(2, $this->stream->write('nm'));
        $this->assertSame($contents, (string) $this->stream);
    }
    
    /**
     * @covers ::getMetadata
     * @covers ::<protected>
     */
    public function testGetMetadata()
    {
        $resource = fopen($this->file, 'r+');
        $metadata = stream_get_meta_data($resource);
        
        $this->assertSame($metadata, $this->stream->getMetadata());
        
        $key = key($metadata);
        $this->assertSame($metadata[$key], $this->stream->getMetadata($key));
        
        $this->assertSame(null, $this->stream->getMetadata('missing'));
    }
    
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
    
    protected function stream($mode = 'r+', $file = null)
    {
        if($file === null) {
            $file = $this->file;
        }
        
        return new \PHPixie\HTTP\Messages\Stream\Implementation($file, $mode);
    }
}