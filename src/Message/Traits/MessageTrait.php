<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Message\Traits;

use Ghostwriter\Http\Contract\Message\MessageInterface;
use Ghostwriter\Http\Contract\Message\StreamInterface;
use function implode;
use function strtr;

/**
 * Trait implementing functionality common to requests and responses.
 */
trait MessageTrait
{
    /** @var array<string,string> Map of lowercase header name => original name at registration */
    private array $headerNames = [];

    /** @var array<string,array<array-key,string>> Map of all registered headers, as original name => array of values */
    private array $headers = [];

    private string $protocol = '1.1';

    private StreamInterface $stream;

    public function getBody(): StreamInterface
    {
        return $this->stream;
    }

    /**
     * @return string[]
     */
    public function getHeader(string $name): array
    {
        $header = $this->normalizeHeaderName($name);

        if (! array_key_exists($header, $this->headerNames)) {
            return [];
        }

        $header = $this->headerNames[$header];

        return $this->headers[$header];
    }

    public function getHeaderLine(string $name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    /**
     * @return mixed[][]|string[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    public function hasHeader(string $name): bool
    {
        return array_key_exists($this->normalizeHeaderName($name), $this->headerNames);
    }

    /**
     * @param mixed[]|string $value
     */
    public function withAddedHeader(string $name, array|string $value): MessageInterface
    {
        return $this;
    }

    public function withBody(StreamInterface $stream): MessageInterface
    {
        return $this;
    }

    /**
     * @param mixed[]|string $value
     */
    public function withHeader(string $name, array|string $value): MessageInterface
    {
        $copy = clone $this;
        $copy->headers[$name] = $value;

        return $copy;
    }

    public function withoutHeader(string $name): MessageInterface
    {
        $copy = clone $this;
        unset($copy->headers[$name]);

        return $copy;
    }

    public function withProtocolVersion(string $version): MessageInterface
    {
        if ($this->protocol === $version) {
            return $this;
        }

        $copy = clone $this;
        $copy->protocol = $version;

        return $copy;
    }

    private function normalizeHeaderName(string $name): string
    {
        return strtr($name, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
    }
}
