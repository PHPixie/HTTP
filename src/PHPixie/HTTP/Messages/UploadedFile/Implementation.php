<?php

namespace PHPixie\HTTP\Messages\UploadedFile;

use RuntimeException;

class Implementation extends \PHPixie\HTTP\Messages\UploadedFile
{
    public function __construct($messages, $file)
    {
        parent::__construct($messages);
        
        $this->clientFilename  = $name;
        $this->file            = $file;
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
}