<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Message\Traits;

use Ghostwriter\Http\Contract\Message\StreamInterface;
use function implode;
use function strtr;

/**
 * Trait implementing functionality common to requests and responses.
 */
trait MessageTrait
{
    //    /**
    //     * Map of lowercase header name => original name at registration.
    //     *
    //     * @var array<string,string>
    //     */
    //    private array $headerNames = [];
    //
    //    /**
    //     * Map of all registered headers, as original name => array of values.
    //     *
    //     * @var array<string,array<array-key,string>>
    //     */
    //    private array $headers = [];
    //
    //    private string $protocol = '1.1';
    //
    //    private StreamInterface $stream;

    public function getBody(): StreamInterface
    {
        return $this->stream;
    }

    /**
     * @return array<array-key,string>
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
     * @return array<string,array<array-key,string>>
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
     * @param array<string,string>|string $value
     */
    public function withAddedHeader(string $name, array|string $value): self
    {
        return $this;
    }

    public function withBody(StreamInterface $stream): self
    {
        return $this;
    }

    /**
     * @param array<array-key,string>|string $value
     */
    public function withHeader(string $name, array|string $value): self
    {
        $copy = clone $this;
        $copy->headers[$name] = $value;

        return $copy;
    }

    public function withoutHeader(string $name): self
    {
        $copy = clone $this;
        unset($copy->headers[$name]);

        return $copy;
    }

    public function withProtocolVersion(string $version): self
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
