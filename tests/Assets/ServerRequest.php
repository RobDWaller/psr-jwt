<?php

declare(strict_types=1);

namespace Tests\Assets;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * @codeCoverageIgnore
 */
class ServerRequest implements ServerRequestInterface
{
    protected $server;

    protected $cookies;

    protected $query;

    protected $body;

    public function __construct(array $server, array $cookies, array $query, $body)
    {
        $this->server = $server;

        $this->cookies = $cookies;

        $this->query = $query;

        $this->body = $body;
    }

    /**
     * Does nothing
     */
    public function getProtocolVersion()
    {
        return '';
    }

    /**
     * Does nothing
     */
    public function withProtocolVersion($version)
    {
        return $version;
    }

    /**
     * Does nothing
     */
    public function getHeaders()
    {
        return [];
    }

    /**
     * Does nothing
     */
    public function hasHeader($name)
    {
        return $name;
    }

    /**
     * Does nothing
     */
    public function getHeader($name)
    {
        return $name;
    }

    /**
     * Does nothing
     */
    public function getHeaderLine($name)
    {
        return $name;
    }

    /**
     * Does nothing
     */
    public function withHeader($name, $value)
    {
        return $value;
    }

    /**
     * Does nothing
     */
    public function withAddedHeader($name, $value)
    {
        return $value;
    }

    /**
     * Does nothing
     */
    public function withoutHeader($name)
    {
        return $name;
    }

    /**
     * Does nothing
     */
    public function getBody()
    {
        return '';
    }

    /**
     * Does nothing
     */
    public function withBody(StreamInterface $body)
    {
        return $body;
    }

    /**
     * Does nothing
     */
    public function getRequestTarget()
    {
        return '';
    }

    /**
     * Does nothing
     */
    public function withRequestTarget($requestTarget)
    {
        return $requestTarget;
    }

    /**
     * Does nothing
     */
    public function getMethod()
    {
        return '';
    }

    /**
     * Does nothing
     */
    public function withMethod($method)
    {
        return $method;
    }

    /**
     * Does nothing
     */
    public function getUri()
    {
        return '';
    }

    /**
     * Does nothing
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        return $uri;
    }

    public function getServerParams(): array
    {
        return $this->server;
    }

    public function getCookieParams(): array
    {
        return $this->cookies;
    }

    /**
     * Does nothing
     */
    public function withCookieParams(array $cookies)
    {
        return $cookies;
    }

    public function getQueryParams(): array
    {
        return $this->query;
    }

    /**
     * Does nothing
     */
    public function withQueryParams(array $query)
    {
        return $query;
    }

    /**
     * Does nothing
     */
    public function getUploadedFiles()
    {
        return '';
    }

    /**
     * Does nothing
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        return '';
    }

    /**
     * @return mixed
     */
    public function getParsedBody()
    {
        return $this->body;
    }

    /**
     * Does nothing
     */
    public function withParsedBody($data)
    {
        return $data;
    }

    /**
     * Does nothing
     */
    public function getAttributes()
    {
        return '';
    }

    /**
     * Does nothing
     */
    public function getAttribute($name, $default = null)
    {
        return $default;
    }

    /**
     * Does nothing
     */
    public function withAttribute($name, $value)
    {
        return $value;
    }

    /**
     * Does nothing
     */
    public function withoutAttribute($name)
    {
        return $name;
    }
}
