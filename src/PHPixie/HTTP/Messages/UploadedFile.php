<?php

namespace PHPixie\HTTP\Messages;

use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

abstract class UploadedFile implements UploadedFileInterface
{
    protected $messages;
    
    protected $clientFilename;
    protected $clientMediaType;
    protected $file;
    protected $error;
    protected $size;
    
    protected $stream;
    
    public function __construct($messages)
    {
        $this->messages = $messages;
    }
    
    public function getStream()
    {
        if($this->stream === null) {
            $this->assertValidUpload();
            $this->stream = $this->messages->stream($this->file);
        }
        
        return $this->stream;
    }

    abstract public function move($path);
    
    public function getSize()
    {
        $this->requireSize();
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
        $this->requireClientMediaType();
        return $this->clientMediaType;
    }
    
    protected function assertValidUpload()
    {
        if($this->error !== UPLOAD_ERR_OK) {
            throw new RuntimeException("File was not successfully uploaded");
        }
    }
    
    protected function requireSize()
    {
    
    }
    
    protected function requireClientMediaType()
    {
    
    }
}
