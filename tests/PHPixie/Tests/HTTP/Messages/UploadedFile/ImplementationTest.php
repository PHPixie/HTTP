<?php

namespace PHPixie\Tests\HTTP\Messages\UploadedFile;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages\UploadedFile\Implementation
 */
class ImplementationTest extends \PHPixie\Tests\HTTP\Messages\UploadedFileTest
{
 
    protected $tempDir;
    
    public function setUp()
    {
        $this->tempDir = sys_get_temp_dir();
        
        parent::setUp();
    }
    
    /**
     * @covers ::getFileBasename
     * @covers ::getFileType
     * @covers ::getFileSize
     */
    public function testFileGetters()
    {
        $this->prepareFile();
        
        $this->clientFilename  = pathinfo($this->file, PATHINFO_BASENAME);
        $this->clientMediaType = filetype($this->file);
        $this->size            = filesize($this->file);
        
        $this->uploadedFile = new \PHPixie\HTTP\Messages\UploadedFile\Implementation(
            $this->messages,
            $this->file
        );
        
        parent::testGetters();
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
            array('clientFilename', 'getFileBasename'),
            array('clientMediaType', 'getFileType', true),
            array('size', 'getFileSize', true),
            array('error')
        );
        
        foreach($getters as $set) {
            $uploadedFile = $this->uploadedFile();
            
            $name = $set[0];
            $method = 'get'.ucfirst($name);
            
            if(isset($set[1])) {
                $this->method($uploadedFile, $set[1], $this->$name, array(), 0, true);
            }
            
            for($i=0; $i<2; $i++) {
                $this->assertSame($this->$name, $uploadedFile->$method());
            }
            
            if(isset($set[2])) {
                $uploadedFile = $this->uploadedFile();
                $this->method($uploadedFile, $set[1], false, array(), 0, true);
                
                $this->assertException(function() use($uploadedFile, $method) {
                    $uploadedFile->$method();
                }, '\RuntimeException');
            }
        }
    }
    
    /**
     * @covers ::moveTo
     * @covers ::<protected>
     */
    public function testMove()
    {
        $destination = 'dest.png';
        
        $this->method($this->uploadedFile, 'moveFile', true, array($destination), 0);
        $this->uploadedFile->moveTo($destination);
        
        $this->method($this->uploadedFile, 'moveFile', false, array($destination), 0);
        
        $uploadedFile = $this->uploadedFile;
        $this->assertException(function() use($uploadedFile, $destination) {
            $uploadedFile->moveTo($destination);
        }, '\RuntimeException');
    }
    
    /**
     * @covers ::getSize
     * @covers ::<protected>
     */
    public function testInvalidFileSize()
    {
        $methods = array(
            'getClientMediaType' => 'getFileType',
            'getSize'            => 'getFileSize'
        );
        
        $uploadedFile = $this->uploadedFile;
        foreach($methods as $method => $failMethod) {
            $this->method($this->uploadedFile, $failMethod, false, array(), 0);
            $this->assertException(function() use($uploadedFile, $method) {
                $uploadedFile->$method();
            }, '\RuntimeException');
        }
    }
    
    /**
     * @covers ::moveTo
     * @covers ::<protected>
     */
    public function testMoveFile()
    {
        $this->prepareFile();
        
        $uploadedFile = new \PHPixie\HTTP\Messages\UploadedFile\Implementation(
            $this->messages,
            $this->file
        );
        
        $destination = tempnam($this->tempDir, 'uploaded_file_dest.php');
        $uploadedFile->moveTo($destination);
        
        $this->assertSame(false, file_exists($this->file));
        $this->assertSame(true, file_exists($destination));
    }
    
    protected function prepareInvalidUpload()
    {
        $this->error = 1;
        return $this->uploadedFile(true);
    }
    
    protected function prepareFile()
    {
        $this->file = tempnam($this->tempDir, 'uploaded_file_src.php');
        file_put_contents($this->file, 'test');
    }
    
    protected function uploadedFile($withParams = false)
    {
        $params = array(
            $this->messages,
            $this->file
        );
        
        if($withParams) {
            $params = array_merge($params, array(
                $this->clientFilename,
                $this->clientMediaType,
                $this->size,
                $this->error
            ));
        }
        
        return $this->getMock(
            '\PHPixie\HTTP\Messages\UploadedFile\Implementation',
            array(
                'getFileBasename',
                'getFileType',
                'getFileSize',
                'moveFile'
            ),
            $params
        );
    }
}