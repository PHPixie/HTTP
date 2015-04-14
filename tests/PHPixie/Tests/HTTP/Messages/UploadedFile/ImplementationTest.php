<?php

namespace PHPixie\Tests\HTTP\Messages\UploadedFile;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages\UploadedFile\Implementation
 */
class ImplementationTest extends \PHPixie\Tests\HTTP\Messages\UploadedFileTest
{
    
    /**
     * @covers ::move
     * @covers ::<protected>
     */
    public function testMove()
    {
        $destination = 'dest.png';
        
        $this->method($this->uploadedFile, 'moveUploadedFile', true, array($destination), 0);
        $this->uploadedFile->move($destination);
        
        $this->method($this->uploadedFile, 'moveUploadedFile', false, array($destination), 0);
        
        $uploadedFile = $this->uploadedFile;
        $this->assertException(function() use($uploadedFile, $destination) {
            $uploadedFile->move($destination);
        }, '\RuntimeException');
    }
    
    /**
     * @covers ::moveUploadedFile
     * @covers ::<protected>
     */
    public function testMoveUploadedFile()
    {
        $uploadedFile = new \PHPixie\HTTP\Messages\UploadedFile\SAPI(
            $this->messages,
            $this->clientFilename,
            $this->clientMediaType,
            $this->file,
            $this->error,
            $this->size
        );
        
        $this->assertException(function() use($uploadedFile) {
            $uploadedFile->move('test');
        }, '\RuntimeException');
    }
        
    protected function uploadedFile()
    {
        return $this->getMock(
            '\PHPixie\HTTP\Messages\UploadedFile\SAPI',
            array('moveFile'),
            array(
                $this->messages,
                $this->clientFilename,
                $this->clientMediaType,
                $this->file,
                $this->error,
                $this->size
            )
        );
    }
}