<?php

namespace PHPixie\HTTP\Responses;

use PHPixie\HTTP\Context;
use PHPixie\HTTP\Data\Headers;
use PHPixie\HTTP\Messages;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * HTTP response representation
 */
class Response
{
    /**
     * @var Messages
     */
    protected $messages;

    /**
     * @var Headers
     */
    protected $headers;

    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @var string
     */
    protected $reasonPhrase;

    /**
     * @var StreamInterface
     */
    protected $body;

    /**
     * Constructor
     * @param Messages $messages
     * @param Headers $headers
     * @param StreamInterface $body
     * @param int $statusCode
     * @param string $reasonPhrase
     */
    public function __construct($messages, $headers, $body, $statusCode = 200, $reasonPhrase = null)
    {
        $this->messages     = $messages;
        $this->headers      = $headers;
        $this->body         = $body;
        $this->statusCode   = $statusCode;
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * Headers
     * @return Headers
     */
    public function headers()
    {
        return $this->headers;
    }

    /**
     * Body stream
     * @return StreamInterface
     */
    public function body()
    {
        return $this->body;
    }

    /**
     * Status code
     * @return int
     */
    public function statusCode()
    {
        return $this->statusCode;
    }

    /**
     * Status phrase
     * @return string|null
     */
    public function reasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * Set status code and phrase
     * @param int $code
     * @param string|null $reasonPhrase
     */
    public function setStatus($code, $reasonPhrase = null)
    {
        $this->statusCode   = $code;
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * Get PSR-7 response representation
     * @param Context|null $context HTTP context
     * @return ResponseInterface
     */
    public function asResponseMessage($context = null)
    {
        return $this->messages->response(
            '1.1',
            $this->mergeContextHeaders($context),
            $this->body,
            $this->statusCode,
            $this->reasonPhrase
        );
    }

    /**
     * Merge headers from HTTP context
     * @param Context $context
     * @return array
     */
    protected function mergeContextHeaders($context)
    {
        $headers = $this->headers->asArray();
        
        if($context === null ) {
            return $headers;
        }
        
        $cookieUpdates = $context->cookies()->updates();
        if(empty($cookieUpdates)) {
            return $headers;
        }
        
        $cookieHeaders = array();
        foreach($cookieUpdates as $update) {
            $cookieHeaders[] = $update->asHeader();
        }
        
        foreach($headers as $name => $value) {
            if(strtolower($name) === 'set-cookie') {
                foreach($cookieHeaders as $header) {
                    $headers[$name][] = $header;
                }
                return $headers;
            }
        }
        
        $headers['Set-Cookie'] = $cookieHeaders;
        return $headers;
    }
}