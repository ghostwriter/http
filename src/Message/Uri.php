<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Message;

use Ghostwriter\Http\Contract\Message\UriInterface;
use InvalidArgumentException;
use Stringable;
use function array_key_exists;
use function parse_url;
use function sprintf;
use function strnatcasecmp;
use function strtr;

final class Uri implements Stringable, UriInterface
{
    /**
     * Scheme names and their corresponding ports.
     *
     * @var array<string,int>
     */
    private const SUPPORTED_SCHEMES = [
        'http' => 80,
        'https' => 443,
    ];

    /**
     * URI fragment.
     *
     * @var string uri fragment
     */
    private string $fragment = '';

    /**
     * URI host.
     *
     * @var string uri host
     */
    private string $host = '';

    /**
     * URI path.
     *
     * @var string uri path
     */
    private string $path = '';

    /**
     * URI port.
     *
     * @var null|int uri port
     */
    private ?int $port = null;

    /**
     * URI query.
     *
     * @var string uri query string
     */
    private string $query = '';

    /**
     * URI scheme without "://" suffix.
     *
     * @var string uri scheme
     */
    private string $scheme = '';

    /**
     * URI user info.
     *
     * @var string uri user info
     */
    private string $userInfo = '';

    public function __construct(string $uri = '')
    {
        if ('' !== $uri) {
            $parts = parse_url($uri);
            if (false === $parts) {
                throw new InvalidArgumentException(sprintf('Unable to parse URI: "%s"', $uri));
            }

            // Apply parse_url parts to a URI.
            $this->scheme = isset($parts['scheme']) ? strtr(
                $parts['scheme'],
                'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
                'abcdefghijklmnopqrstuvwxyz'
            ) : '';

            $this->userInfo = $parts['user'] ?? '';

            if (isset($parts['pass'])) {
                $this->userInfo .= ':' . $parts['pass'];
            }

            $this->host = $this->sanitizeHost(strtr(
                $parts['host'] ?? '',
                'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
                'abcdefghijklmnopqrstuvwxyz'
            ));

            $this->port = $this->sanitizePort($parts['port'] ?? null);
            $this->path = $this->sanitizePath($parts['path']);
            $this->query = $this->sanitizeQueryOrFragment($parts['query'] ??'');
            $this->fragment = $this->sanitizeQueryOrFragment($parts['fragment'] ?? '');
        }
    }

    public function __toString(): string
    {
        $scheme = $this->scheme;
        $authority = $this->getAuthority();
        $path = $this->path;
        $query = $this->query;
        $fragment = $this->fragment;

        if ('' !== $path) {
            if ('/' !== $path[0]) {
                if ('' !== $authority) {
                    // If the path is rootless and an authority is present, the path MUST be prefixed by "/".
                    $path = '/' . $path;
                }
            } elseif (isset($path[1]) && '/' === $path[1]) {
                if ('' === $authority) {
                    // If the path is starting with more than one "/" and no authority is present,
                    // the starting slashes MUST be reduced to one.
                    $path = '/' . ltrim($path, '/');
                }
            }
        }

        return ('' !== $scheme ? $scheme . ':' : '')
            . ('' !== $authority ? '//' . $authority : '')
            . $path
            . ('' !== $query ? '?' . $query : '')
            . ('' !== $fragment ? '#' . $fragment : '');
    }

    public function getAuthority(): string
    {
        if ('' === $this->host) {
            return '';
        }

        $authority = $this->host;
        if ('' !== $this->userInfo) {
            $authority = $this->userInfo . '@' . $authority;
        }

        if (null !== $this->port) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    public function withFragment(string $fragment): UriInterface
    {
        $clone = clone $this;
        $clone->fragment = $this->sanitizeFragment($fragment);

        return $clone;
    }

    public function withHost(string $host): UriInterface
    {
        throw new InvalidArgumentException();
    }

    public function withPath(string $path): UriInterface
    {
        throw new InvalidArgumentException();
    }

    public function withPort(?int $port): UriInterface
    {
        throw new InvalidArgumentException();
    }

    public function withQuery(string $query): UriInterface
    {
        $clone = clone $this;
        $clone->query = $this->sanitizeQuery($query);

        return $clone;
    }

    public function withScheme(string $scheme): UriInterface
    {
        if (0 === strnatcasecmp($scheme, $this->scheme)) {
            return $this;
        }

        $scheme = $this->sanitizeScheme($scheme);

        $clone = clone $this;
        $clone->scheme = $scheme;

        if (null !== $clone->port) {
            return $clone;
        }

        if (array_key_exists($scheme, self::SUPPORTED_SCHEMES)) {
            $clone->port = self::SUPPORTED_SCHEMES[$scheme];
        }

        return $clone;
    }

    public function withUserInfo(string $user, ?string $password = null): UriInterface
    {
        if (null !== $password && '' !== $password) {
            $user .= ':' . $password;
        }

        if ($this->userInfo === $user) {
            return $this;
        }

        $clone = clone $this;
        $clone->userInfo = $user;

        return $clone;
    }

    /**
     * Sanitize the URI fragment.
     *
     * @throws InvalidArgumentException for invalid fragment strings
     */
    private function sanitizeFragment(string $fragment): string
    {
        $fragment = ltrim($fragment, '#');

        return $this->sanitizeQueryOrFragment($fragment);
    }

    /**
     * Sanitize the URI host.
     *
     * @throws InvalidArgumentException for invalid host
     */
    private function sanitizeHost(string $host): string
    {
        if ('file' === $this->scheme && 'localhost' === $host) {
            $this->host = $host;
            return $host;
        }

        $this->host = $host;

        return $host;
    }

    /**
     * Sanitize the URI path.
     *
     * @throws InvalidArgumentException for invalid paths
     */
    private function sanitizePath(string $path): ?string
    {
        if (str_contains($path, '?')) {
            throw new InvalidArgumentException('The URI path must not contain a query string');
        }

        if (str_contains($path, '#')) {
            throw new InvalidArgumentException('The URI path must not contain a URI fragment');
        }

        return preg_replace_callback(
            '#(?:[^a-zA-Z0-9_\-\.~:@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))#',
            static fn ($match): string => rawurlencode($match[0]),
            $path
        );
    }

    /**
     * Sanitize the URI port.
     *
     * @throws InvalidArgumentException for invalid ports
     */
    private function sanitizePort(?int $port): ?int
    {
        if (null === $port) {
            return null;
        }

        if ($port < 0 || $port > 65535) {
            throw new InvalidArgumentException(sprintf(
                'Invalid port "%d" specified; Must be a valid TCP/UDP port between 0 and 65535',
                $port
            ));
        }

        return $port;
    }

    /**
     * Sanitize the URI query string.
     *
     * @throws InvalidArgumentException for invalid query strings
     */
    private function sanitizeQuery(string $query): string
    {
        if (str_contains($query, '#')) {
            throw new InvalidArgumentException('The URI query must not contain a URI fragment');
        }

        $query = ltrim($query, '?');
        $parts = explode('&', $query);

        foreach ($parts as $index => $part) {
            $data = explode('=', $part, 2);

            if (1 === count($data)) {
                $data[] = null;
            }

            [$key, $value] = $data;

            if (null === $value) {
                $parts[$index] = $this->sanitizeQueryOrFragment($key);
                continue;
            }

            $parts[$index] = $this->sanitizeQueryOrFragment($key) . '=' . $this->sanitizeQueryOrFragment($value);
        }

        return implode('&', $parts);
    }

    /**
     * Sanitize the URI query string or fragment.
     *
     * SUB_DELIMITERS|UNRESERVED_CHARACTERS
     */
    private function sanitizeQueryOrFragment(string $value): ?string
    {
        return preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/',
            static fn ($matches): string => rawurlencode($matches[0]),
            $value
        );
    }

    /**
     * Sanitize the URI scheme.
     *
     * @throws InvalidArgumentException for invalid or unsupported schemes
     */
    private function sanitizeScheme(string $scheme): string
    {
        $scheme = strtolower($scheme);
        $scheme = rtrim($scheme, '://');
        if ('' === $scheme) {
            return $scheme;
        }
        if (array_key_exists($scheme, self::SUPPORTED_SCHEMES)) {
            return $scheme;
        }
        throw new InvalidArgumentException("The URI scheme must be '', http or https");
    }
}
