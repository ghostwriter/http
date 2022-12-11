<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Message\Traits;

use Ghostwriter\Http\Contract\Message\UriInterface;
use InvalidArgumentException;
use function preg_match;

/**
 * Trait implementing functionality common to requests and responses.
 */
trait RequestTrait
{
    use MessageTrait;

    private string $method;

    private ?string $requestTarget = null;

    private ?UriInterface $uri = null;

    public function __clone(): void
    {
        if ($this->uri instanceof UriInterface) {
            $this->uri = clone $this->uri;
        }
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getRequestTarget(): string
    {
        if (null !== $this->requestTarget) {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath();
        if ('' === $target) {
            $target = '/';
        }

        if ('' !== $this->uri->getQuery()) {
            $target .= '?' . $this->uri->getQuery();
        }

        if ('' !== $this->uri->getFragment()) {
            $target .= '#' . $this->uri->getFragment();
        }

        return $target;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withMethod(string $method): self
    {
        if ($method === $this->method) {
            return $this;
        }

        $clone = clone $this;
        $clone->method = $method;

        return $clone;
    }

    public function withRequestTarget(mixed $requestTarget): self
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new InvalidArgumentException('Invalid request target provided; cannot contain whitespace');
        }

        $new = clone $this;
        $new->requestTarget = $requestTarget;

        return $new;
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): self
    {
        if ($uri === $this->uri) {
            return $this;
        }

        $clone = clone $this;
        $clone->uri = $uri;

        if (! $preserveHost || ! $this->hasHeader('Host')) {
            $clone->updateHostFromUri();
        }

        return $clone;
    }

    private function updateHostFromUri(): void
    {
        $host = $this->uri->getHost();
        if ('' === $host) {
            return;
        }

        $port = $this->uri->getPort();
        if (null !== $port) {
            $host .= ':' . $port;
        }

        if (array_key_exists('host', $this->headerNames)) {
            $header = $this->headerNames['host'];
        } else {
            $this->headerNames['host'] = 'Host';
            $header = 'Host';
        }

        // Ensure Host is the first header.
        // See: http://tools.ietf.org/html/rfc7230#section-5.4
        $this->headers = [
            $header => [$host],
        ] + $this->headers;
    }
}
