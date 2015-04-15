<?php

namespace PHPixie\HTTP\Messages\UploadedFile;

use RuntimeException;

class SAPI extends \PHPixie\HTTP\Messages\UploadedFile
{
    public function __construct($http, $fileData)
    {
        parent::__construct($http);
        
        $this->clientFilename  = $fileData['name'];
        $this->clientMediaType = $fileData['type'];
        $this->file            = $fileData['tmp_name'];
        $this->error           = $fileData['error'];
        $this->size            = $fileData['size'];
    }
    
    public function move($path)
    {
        $this->assertValidUpload();
        if(!$this->moveUploadedFile($path)) {
            throw new RuntimeException("Failed to move uploaded file '{$this->file}' to '$path'");
        }
    }
    
    protected function moveUploadedFile($path)
    {
        return move_uploaded_file($this->file, $path);
    }
}