<?php
namespace PHPixie\HTTP\Messages\Message;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * PSR-7 Response Implementation
 */
class Response extends    Implementation
              implements ResponseInterface
{
    /**
     * @var array
     */
    protected static $codePhrases = array(
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        // SERVER ERROR
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    );
    
    /**
     * @var null|string
     */
    private $reasonPhrase;
    
    /**
     * @var int
     */
    private $statusCode = 200;

    /**
     * Constructor
     * @param string $protocolVersion
     * @param array $headers
     * @param StreamInterface $body
     * @param int $statusCode
     * @param string|null $reasonPhrase
     */
    public function __construct($protocolVersion, $headers, $body, $statusCode = 200, $reasonPhrase = null)
    {
        parent::__construct($protocolVersion, $headers, $body);
        
        $this->validateStatusCode($statusCode);
        if($reasonPhrase === null) {
            $reasonPhrase = $this->statusCodePhrase($statusCode);
        }
        
        $this->statusCode   = $statusCode;
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * @param int $statusCode
     * @return string|null
     */
    protected function statusCodePhrase($statusCode)
    {
        if(array_key_exists($statusCode, static::$codePhrases)) {
            return static::$codePhrases[$statusCode];
        }
        
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @inheritdoc
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * @inheritdoc
     */
    public function withStatus($code, $reasonPhrase = null)
    {
        $this->validateStatusCode($code);
        if($reasonPhrase === null) {
            $reasonPhrase = $this->statusCodePhrase($code);
        }
        
        $new = clone $this;
        $new->statusCode = $code;
        $new->reasonPhrase = $reasonPhrase;
        return $new;
    }

    /**
     * @param int $code
     * @throws \InvalidArgumentException
     */
    protected function validateStatusCode($code)
    {
        if ($code < 100 || $code > 599) {
            throw new \InvalidArgumentException("Invalid status '$code', must be between 100 and 599");
        }
    }
}