<?php

namespace PHPixie\Tests\HTTP\Messages\Message;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages\Message\Response
 */
class ResponseTest extends ImplementationTest
{
    protected $statusCode   = 200;
    protected $reasonPhrase = 'Pixie';
    
    public function message()
    {
        return new \PHPixie\HTTP\Messages\Message\Response(
            $this->protocolVersion,
            $this->headers,
            $this->body,
            $this->statusCode,
            $this->reasonPhrase
        );
    }
}