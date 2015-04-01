<?php

namespace PHPixie\Tests\HTTP\Messages\Message;

/**
 * @coversDefaultClass PHPixie\HTTP\Messages\Message\Implementation
 */
class ImplementationTest extends \PHPixie\Tests\HTTP\Messages\MessageTest
{

    public function message()
    {
        return new \PHPixie\HTTP\Messages\Message\Implementation(
            $this->protocolVersion,
            $this->headers,
            $this->body
        );
    }
}