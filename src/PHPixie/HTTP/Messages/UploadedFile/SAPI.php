<?php

namespace PHPixie\HTTP\Messages\UploadedFile;

use RuntimeException;

class SAPI extends \PHPixie\HTTP\Messages\UploadedFile
{
    public function __construct($fileData)
    {
        $this->clientFilename  = $fileData['name'];
        $this->clientMediaType = $fileData['type'];
        $this->file            = $fileData['tmp_name'];
        $this->error           = $fileData['error'];
        $this->size            = $fileData['size'];
    }
    
    public function moveFile($path)
    {
        move_uploaded_file($this->file, $path);
    }
}