<?php

namespace PHPixie\HTTP\Messages\UploadedFile;

use RuntimeException;

class Implementation extends \PHPixie\HTTP\Messages\UploadedFile
{
    public function __construct($http, $file, $clientFilename = null, $clientMediaType = null, $size = null, $error = 0)
    {
        parent::__construct($http);
        
        $this->file            = $file;
        $this->clientFilename  = $clientFilename;
        $this->clientMediaType = $clientMediaType;
        $this->size            = $size;
        $this->error           = $error;
    }

    public function getClientFilename()
    {
        if($this->clientFilename === null) {
            $this->clientFilename = $this->getFileBasename();
        }
        
        return $this->clientFilename;
    }
    
    public function getClientMediaType()
    {
        if($this->clientMediaType === null) {
            if(($type = $this->getFileType()) === false) {
                throw new RuntimeException("Could not determine the file type of '{$this->file}'");
            }
            
            $this->clientMediaType = $type;
        }
        
        return $this->clientMediaType;
    }
    
    public function getSize()
    {
        if($this->size === null) {
            if(($size = $this->getFileSize()) === false) {
                throw new RuntimeException("Could not determine the file size of '{$this->file}'");
            }
            
            $this->size = $size;
        }
        
        return $this->size;
    }
    
    public function move($path)
    {
        $this->assertValidUpload();
        if(!$this->moveFile($path)) {
            throw new RuntimeException("Failed to move uploaded file '{$this->file}' to '$path'");
        }
    }
    
    protected function moveFile($path)
    {
        return rename($this->file, $path);
    }
    
    protected function getFileBasename()
    {
        return pathinfo($this->file, PATHINFO_BASENAME);
    }
    
    protected function getFileType()
    {
        return filetype($this->file);
    }
    
    protected function getFileSize()
    {
        return filesize($this->file);
    }
}