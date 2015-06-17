<?php

namespace PHPixie\Tests\HTTP\Context\Container;

/**
 * @coversDefaultClass PHPixie\HTTP\Context\Container\Implementation
 */
class ImplementationTest extends \PHPixie\Test\Testcase
{
    protected $context;
    protected $container;
    
    public function setUp()
    {
        $this->context = $this->quickMock('\PHPixie\Tests\HTTP\Context');
        $this->container = new \PHPixie\HTTP\Context\Container\Implementation(
            $this->context
        );
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
    
    }
    
    /**
     * @covers ::httpContext
     * @covers ::<protected>
     */
    public function testHttpContext()
    {
        $this->assertSame($this->context, $this->container->httpContext());
    }
}