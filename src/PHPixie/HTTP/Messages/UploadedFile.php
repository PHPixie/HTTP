<?php

namespace PHPixie\HTTP\Messages;

use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

class UploadedFile implements UploadedFileInterface
{
    protected $clientFilename;
    protected $clientMediaType;
    protected $uploadedFilename;
    protected $error;
    protected $size;
    
    public function getStream()
    {
    
    }

    public function move($path)
    {
        $this->assertValidUpload();
        $this->moveFile($path);
    }
    
    protected abstract function moveFile($path);
    
    public function getSize()
    {
        return $this->size;
    }
    
    public function getError()
    {
        return $this->error;
    }
    
    public function getClientFilename()
    {
        return $this->clientFilename;
    }
    
    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }
    
    protected function assertValidUpload()
    {
        if($this->error !== UPLOAD_ERR_OK) {
            throw new RuntimeException("File was not successfully uploaded");
        }
    }
}
