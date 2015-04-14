<?php

namespace PHPixie\Tests\HTTP\Messages\UploadedFile;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages\UploadedFile\SAPI
 */
class SAPITest extends \PHPixie\Tests\HTTP\Messages\UploadedFileTest
{    
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
        $fileData = $this->getFileData();
        return new \PHPixie\HTTP\Messages\UploadedFile\SAPI($fileData);
    }
}