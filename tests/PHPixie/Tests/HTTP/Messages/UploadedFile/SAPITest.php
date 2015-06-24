<?php

namespace PHPixie\Tests\HTTP\Messages\UploadedFile;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages\UploadedFile\SAPI
 */
class SAPITest extends \PHPixie\Tests\HTTP\Messages\UploadedFileTest
{
    
    /**
     * @covers ::moveTo
     * @covers ::<protected>
     */
    public function testMove()
    {
        $destination = 'dest.png';
        
        $this->method($this->uploadedFile, 'moveUploadedFile', true, array($destination), 0);
        $this->uploadedFile->moveTo($destination);
        
        $this->method($this->uploadedFile, 'moveUploadedFile', false, array($destination), 0);
        
        $uploadedFile = $this->uploadedFile;
        $this->assertException(function() use($uploadedFile, $destination) {
            $uploadedFile->moveTo($destination);
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
            $this->getFileData()
        );
        
        $this->assertException(function() use($uploadedFile) {
            $uploadedFile->moveTo('test');
        }, '\RuntimeException');
    }
        
    protected function getFileData()
    {
        return array(
            'name'     => $this->clientFilename,
            'type'     => $this->clientMediaType,
            'tmp_name' => $this->file,
            'error'    => $this->error,
            'size'     => $this->size
        );
    }
    
    protected function uploadedFile()
    {
        return $this->getMock(
            '\PHPixie\HTTP\Messages\UploadedFile\SAPI',
            array('moveUploadedFile'),
            array(
                $this->messages,
                $this->getFileData()
            )
        );
    }
    
    protected function prepareInvalidUpload()
    {
        $this->error = 1;
        return $this->uploadedFile();
    }
}