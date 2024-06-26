<?php

declare(strict_types=1);

namespace Httpful;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

class ServerRequest extends Request implements ServerRequestInterface
{
    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var array
     */
    private $cookieParams = [];

    /**
     * @var array|object|null
     */
    private $parsedBody;

    /**
     * @var array
     */
    private $queryParams = [];

    /**
     * @var array
     */
    private $serverParams;

    /**
     * @var UploadedFileInterface[]
     */
    private $uploadedFiles = [];

    /**
     * @param string|null $method       Http Method
     * @param string|null $mime         Mime Type to Use
     * @param static|null $template     "Request"-template object
     * @param array       $serverParams Typically the $_SERVER (superglobal)
     */
    public function __construct(
        string $method = null,
        string $mime = null,
        self $template = null,
        array $serverParams = []
    ) {
        $this->serverParams = $serverParams;

        parent::__construct($method, $mime, $template);
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed|null
     */
    public function getAttribute($name, $default = null)
    {
        if (\array_key_exists($name, $this->attributes) === false) {
            return $default;
        }

        return $this->attributes[$name];
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return array
     */
    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    /**
     * @return array|object|null
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * @return array
     */
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    /**
     * @return array
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return static
     */
    public function withAttribute($name, $value): self
    {
        $new = clone $this;
        $new->attributes[$name] = $value;

        return $new;
    }

    /**
     * @param array $cookies
     *
     * @return ServerRequest|ServerRequestInterface
     */
    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        $new = clone $this;
        $new->cookieParams = $cookies;

        return $new;
    }

    /**
     * @param array|object|null $data
     *
     * @return ServerRequest|ServerRequestInterface
     */
    public function withParsedBody($data): ServerRequestInterface
    {
        if (
            !\is_array($data)
            &&
            !\is_object($data)
            &&
            $data !== null
        ) {
            throw new \InvalidArgumentException('First parameter to withParsedBody MUST be object, array or null');
        }

        $new = clone $this;
        $new->parsedBody = $data;

        return $new;
    }

    /**
     * @param array $query
     *
     * @return ServerRequestInterface|static
     */
    public function withQueryParams(array $query): ServerRequestInterface
    {
        $new = clone $this;
        $new->queryParams = $query;

        return $new;
    }

    /**
     * @param array $uploadedFiles
     *
     * @return ServerRequestInterface|static
     */
    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        $new = clone $this;
        $new->uploadedFiles = $uploadedFiles;

        return $new;
    }

    /**
     * @param string $name
     *
     * @return static
     */
    public function withoutAttribute($name): self
    {
        if (\array_key_exists($name, $this->attributes) === false) {
            return $this;
        }

        $new = clone $this;
        unset($new->attributes[$name]);

        return $new;
    }
}
