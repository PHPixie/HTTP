<?php

namespace PHPixie\HTTP\Messages\Message;

class Implementation extends \PHPixie\HTTP\Messages\Message
{
    public function __construct($protocolVersion, $headers, $body)
    {
        $this->validateHeaders($headers);
        
        $this->protocolVersion = $protocolVersion;
        $this->headers         = $headers;
        $this->body            = $body;
    }
}