<?php

namespace PHPixie\HTTP\Messages\Message\Request\ServerRequest;

class Global extends \PHPixie\HTTP\Messages\Message\Request\ServerRequest
{
    public function __construct($server, $get, $post, $cookies, $files, $body)
    {
        $this->method          = $server['REQUEST_METHOD'];
        $this->serverParams    = $server;
        $this->queryParams     = $get;
        $this->parsedBody      = $post;
        $this->cookieParams    = $cookies;
        $this->fileParams      = $files;
        $this->body            = $body;
    }
    
    protected function requireProtocolVersion()
    {
        $this->protocolVersion = substr($this->server['SERVER_PROTOCOL'], 5);
    }
    
    protected function requireHeaders()
    {
        if($this->processedHeaders) {
            return;
        }
        
        foreach($this->server as $key => $value) {
            if($name === 'HTTP_COOKIE') {
                continue;
            }
            
            if (substr($key, 0, 5) === 'HTTP_') {
                $headerName = substr($key, 5);
                $headerName = $this->normalizeHeaderName($headerName);
                $headers[$headerName] = $value;
                
            }elseif(substr($key, 0, 8) === 'CONTENT_') {
                $headerName = substr($key, 8);
                $headerName = 'Content-' . $this->normalizeHeaderName($headerName);
                $headers[$headerName] = $value;
            }
            
        }
        
        $this->headers = $headers;
        $this->populateHeaderNames();
        
        $this->processedHeaders = true;
    }
    
    public static function marshalUriFromServer(array $server, array $headers)
    {
        $uri = new Uri('');
        // URI scheme
        $scheme = 'http';
        $https  = self::get('HTTPS', $server);
        if (($https && 'off' !== $https)
            || self::getHeader('x-forwarded-proto', $headers, false) === 'https'
        ) {
            $scheme = 'https';
        }
        if (! empty($scheme)) {
            $uri = $uri->withScheme($scheme);
        }
        // Set the host
        $accumulator = (object) ['host' => '', 'port' => null];
        self::marshalHostAndPortFromHeaders($accumulator, $server, $headers);
        $host = $accumulator->host;
        $port = $accumulator->port;
        if (! empty($host)) {
            $uri = $uri->withHost($host);
            if (! empty($port)) {
                $uri = $uri->withPort($port);
            }
        }
        // URI path
        $path = self::marshalRequestUri($server);
        $path = self::stripQueryString($path);
        // URI query
        $query = '';
        if (isset($server['QUERY_STRING'])) {
            $query = ltrim($server['QUERY_STRING'], '?');
        }
        return $uri
            ->withPath($path)
            ->withQuery($query);
    }

    protected function normalizeHeaderName($header)
    {
        $header = strtolower($header);
        $header = str_replace('-', ' ', $header);
        $header = ucwords($header);
        $header = str_replace(' ', '-', $header);
        return $header;
    }
}