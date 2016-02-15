<?php

namespace PHPixie\HTTP\Messages;

use PHPixie\HTTP\Messages;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

/**
 * Base PSR-7 UploadedFile implementation
 */
abstract class UploadedFile implements UploadedFileInterface
{
    /**
     * @var Messages
     */
    protected $messages;

    /**
     * @var string
     */
    protected $clientFilename;

    /**
     * @var string
     */
    protected $clientMediaType;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var int
     */
    protected $error;

    /**
     * @var int|null
     */
    protected $size;

    /**
     * @var StreamInterface
     */
    protected $stream;

    /**
     * Constructor
     * @param Messages $messages
     */
    public function __construct($messages)
    {
        $this->messages = $messages;
    }

    /**
     * @inheritdoc
     */
    public function getStream()
    {
        if($this->stream === null) {
            $this->assertValidUpload();
            $this->stream = $this->messages->stream($this->file);
        }
        
        return $this->stream;
    }

    /**
     * @inheritdoc
     */
    public function getClientFilename()
    {
        return $this->clientFilename;
    }

    /**
     * @inheritdoc
     */
    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }

    /**
     * @inheritdoc
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @inheritdoc
     */
    public function getSize()
    {
        return $this->size;
    }
    
    protected function assertValidUpload()
    {
        if($this->error !== UPLOAD_ERR_OK) {
            throw new RuntimeException("File was not successfully uploaded");
        }
    }

    /**
     * @inheritdoc
     */
    abstract public function moveTo($path);
}
