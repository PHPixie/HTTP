<?php

namespace PHPixie\HTTP\Messages\UploadedFile;

use PHPixie\HTTP\Messages;
use RuntimeException;

/**
 * PSR-7 UploadedFile implementation using $_FILES
 */
class SAPI extends \PHPixie\HTTP\Messages\UploadedFile
{
    /**
     * Construct
     * @param Messages $messages
     * @param array $fileData
     */
    public function __construct($messages, $fileData)
    {
        parent::__construct($messages);
        
        $this->clientFilename  = $fileData['name'];
        $this->clientMediaType = $fileData['type'];
        $this->file            = $fileData['tmp_name'];
        $this->error           = $fileData['error'];
        $this->size            = $fileData['size'];
    }

    /**
     * @inheritdoc
     */
    public function moveTo($path)
    {
        $this->assertValidUpload();
        if(!$this->moveUploadedFile($path)) {
            throw new RuntimeException("Failed to move uploaded file '{$this->file}' to '$path'");
        }
    }

    /**
     * @param $path
     * @return bool
     */
    protected function moveUploadedFile($path)
    {
        return move_uploaded_file($this->file, $path);
    }
}