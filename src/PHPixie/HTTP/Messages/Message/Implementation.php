<?php

namespace PHPixie\HTTP\Messages\Message;

use Psr\Http\Message\StreamInterface;

/**
 * PSR-7 Message implementation
 */
class Implementation extends \PHPixie\HTTP\Messages\Message
{
    /**
     * Constructor
     * @param string $protocolVersion
     * @param array $headers
     * @param StreamInterface $body
     */
    public function __construct($protocolVersion, $headers, $body)
    {
        $this->validateHeaders($headers);
        
        $this->protocolVersion = $protocolVersion;
        $this->headers         = $headers;
        $this->body            = $body;
    }
}