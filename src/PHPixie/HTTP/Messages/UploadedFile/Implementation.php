<?php

namespace PHPixie\HTTP\Messages\UploadedFile;

use PHPixie\HTTP\Messages;
use RuntimeException;

/**
 * PSR-7 UploadedFile implementation
 */
class Implementation extends \PHPixie\HTTP\Messages\UploadedFile
{
    /**
     * Constructor
     * @param Messages $messages
     * @param string $file
     * @param string|null $clientFilename
     * @param string|null $clientMediaType
     * @param int|null $size
     * @param int $error
     */
    public function __construct(
        $messages,
        $file,
        $clientFilename = null,
        $clientMediaType = null,
        $size = null,
        $error = UPLOAD_ERR_OK
    )
    {
        parent::__construct($messages);
        
        $this->file            = $file;
        $this->clientFilename  = $clientFilename;
        $this->clientMediaType = $clientMediaType;
        $this->size            = $size;
        $this->error           = $error;
    }

    /**
     * @inheritdoc
     */
    public function getClientFilename()
    {
        if($this->clientFilename === null) {
            $this->clientFilename = $this->getFileBasename();
        }
        
        return $this->clientFilename;
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function moveTo($path)
    {
        $this->assertValidUpload();
        if(!$this->moveFile($path)) {
            throw new RuntimeException("Failed to move uploaded file '{$this->file}' to '$path'");
        }
    }

    /**
     * @param $path
     * @return bool
     */
    protected function moveFile($path)
    {
        return rename($this->file, $path);
    }

    /**
     * @return string
     */
    protected function getFileBasename()
    {
        return pathinfo($this->file, PATHINFO_BASENAME);
    }

    /**
     * @return string
     */
    protected function getFileType()
    {
        return filetype($this->file);
    }

    /**
     * @return int
     */
    protected function getFileSize()
    {
        return filesize($this->file);
    }
}