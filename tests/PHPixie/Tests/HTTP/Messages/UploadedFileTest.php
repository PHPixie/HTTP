<?php

namespace PHPixie\Tests\HTTP\Messages;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages\UploadedFile
 */
abstract class UploadedFileTest extends \PHPixie\Test\Testcase
{
    protected $clientFilename;
    protected $clientMediaType;
    protected $file;
    protected $error = 0;
    protected $size;
    
    protected $uploadedFile;
    
    protected $destination;
    
    public function setUp()
    {
        $tempDir = sys_get_temp_dir();
        
        $this->file        = tempnam($tempDir, 'uploaded_file_src');
        $this->destination = tempnam($tempDir, 'uploaded_file_dest');
        
        $this->uploadedFile = $this->uploadedFile();
    }
    
    public function tearDown()
    {
        if(is_file($this->file)) {
            unlink($this->file);
        }
        
        if(is_file($this->destination)) {
            unlink($this->destination);
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
     * @covers ::move
     * @covers ::<protected>
     */
    public function testMove()
    {
        file_put_contents($this->file, 'test');
        $this->uploadedFile->move($this->destination);
        
        //$this->assertSame(false, file_exists($this->file));
        $this->assertSame('test', file_get_contents($this->destination));
    }
    
    /**
     * @covers ::getClientFilename
     * @covers ::getClientMediaType
     * @covers ::getError
     * @covers ::getSize
     * @covers ::<protected>
     */
    public function testGetters()
    {
        $getters = array(
            'clientFilename',
            'clientMediaType',
            'error',
            'size'
        );
        
        foreach($getters as $name) {
            $method = 'get'.ucfirst($name);
            $this->assertSame($this->$name, $this->uploadedFile->$method());
        }
    }
    
    protected abstract function uploadedFile();
}