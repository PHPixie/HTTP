<?php
namespace Phly\Http;

use InvalidArgumentException;
use Psr\Http\Message\StreamableInterface;

/**
 * Trait implementing the various methods defined in
 * \Psr\Http\Message\MessageInterface.
 *
 * @link https://github.com/php-fig/http-message/tree/master/src/MessageInterface.php
 */
trait MessageTrait
{
    /**
     * List of all registered headers, as key => array of values.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Map of normalized header name to original name used to register header.
     *
     * @var array
     */
    protected $headerNames = [];

    /**
     * @var string
     */
    private $protocol = '1.1';

    /**
     * @var StreamableInterface
     */
    private $stream;

    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion($version)
    {
        $this->checkUpdate('protocolVersion', $version);
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasHeader($header)
    {
        $header = strtolower($header);
        return array_key_exists($header, $this->headerNames, true);
    }

    public function getHeader($header)
    {
        $lines = $this->getHeaderLines($header);
        
        if (count($lines) === 0) {
            return null;
        }

        return implode(',', $lines);
    }

    public function getHeaderLines($header)
    {
        if (!$this->hasHeader($header)) {
            return array();
        }

        $header = $this->headerNames[strtolower($header)];
        $value = $this->headers[$header];
        return $value;
    }

    public function withHeader($header, $value)
    {
        if (!is_array($value)) {
            $value = array($value);
        }
        
        $headers = $this->headers;
        $lower = strtolower($header);
        if(array_key_exists($lower, $this->headerNames, true)) {
            $normalized = $this->headerNames[$lower];
            unset($headers[$normalized]);
        }
        
        $headers[$header] = $value;
        
        return $this->update('headers', $headers);
    }

    public function withAddedHeader($header, $value)
    {
        if (!is_array($value)) {
            $value = array($value);
        }

        $lines = $this->getHeaderLines($header);
        foreach($value as $line) {
            $lines[]= $line;
        }
        
        return $this->withHeader($header, $lines);
    }

    public function withoutHeader($header)
    {
        $header = strtolower($header);
        $normalized = $this->headerNames[$header];
        
        $headers = $this->headers;
        unset($headers[$normalized]);
        
        return $this->update('headers', $headers);
    }

    public function getBody()
    {
        return $this->stream;
    }

    public function withBody(StreamableInterface $body)
    {
        return $this->update('body', $body);
    }
    
}