<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Message;

use Ghostwriter\Http\Contract\Message\UriInterface;
use Ghostwriter\Http\Message\Exception\InvalidArgumentException;
use function array_key_exists;
use function array_keys;
use function implode;
use function ltrim;
use function parse_url;
use function preg_match;
use function preg_replace_callback;
use function rawurlencode;
use function str_contains;
use function str_starts_with;
use function strtr;
use function substr;

final class Uri implements UriInterface
{
    /**
     * Sub-delims for use in a regex.
     *
     * @link https://tools.ietf.org/html/rfc3986#section-2.2
     *
     * @var string
     */
    public const CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';

    /**
     * Unreserved characters for use in a regex.
     *
     * @link https://tools.ietf.org/html/rfc3986#section-2.3
     *
     * @var string
     */
    public const CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~\pL';

    /**
     * The largest allowed port range.
     *
     * @var int
     */
    public const PORT_MAX = 0xffff;

    /**
     * The smallest allowed port range.
     *
     * @var int
     */
    public const PORT_MIN = 0x0;

    /**
     * Standard scheme names and their corresponding standard ports.
     *
     * @var array<string,int>
     */
    private const SCHEME_STANDARD_PORTS = [
        'http' => 80,
        'https' => 443,
    ];

    /**
     * URI authority cache.
     */
    private ?string $authority = null;

    /**
     * URI fragment.
     */
    private string $fragment = '';

    /**
     * URI host.
     */
    private string $host = '';

    /**
     * URI path.
     */
    private string $path = '';

    /**
     * URI port.
     */
    private ?int $port = null;

    /**
     * URI query.
     */
    private string $query = '';

    /**
     * URI scheme.
     */
    private string $scheme = '';

    /**
     * URI string cache.
     */
    private ?string $uri = null;

    /**
     * URI user info.
     */
    private string $userInfo = '';

    public function __construct(string $uri = '')
    {
        if ('' === $uri) {
            return;
        }

        $parts = parse_url($uri);
        if (false === $parts) {
            throw InvalidArgumentException::invalidUri($uri);
        }

        $this->scheme = isset($parts['scheme']) ? $this->sanitizeScheme($parts['scheme']) : '';
        $this->userInfo = isset($parts['user']) ? $this->sanitizeUserInfo($parts['user'], $parts['pass'] ?? null) : '';
        $this->host = isset($parts['host']) ? $this->sanitizeHost($parts['host']) : '';
        $this->port = isset($parts['port']) ? $this->sanitizePort($parts['port']) : null;
        $this->path = isset($parts['path']) ? $this->sanitizePath($parts['path']) : '';
        $this->query = isset($parts['query']) ? $this->sanitizeQuery($parts['query']) : '';
        $this->fragment = isset($parts['fragment']) ? $this->sanitizeFragment($parts['fragment']) : '';
    }

    /**
     * Resets cache.
     */
    public function __clone()
    {
        $this->uri = null;
    }

    public function __toString(): string
    {
        if (null !== $this->uri) {
            return $this->uri;
        }

        $uri = '';
        $scheme = $this->scheme;
        if ('' !== $scheme) {
            $uri .= $scheme . ':';
        }

        $authority = $this->getAuthority();
        if ('' !== $authority) {
            $uri .= '//' . $authority;
        }

        $path = $this->path;
        if ('' !== $path && ! str_starts_with($path, '/')) {
            $path = '/' . $path;
        }

        $uri .= $path;
        $query = $this->query;
        if ('' !== $query) {
            $uri .= '?' . $query;
        }

        $fragment = $this->fragment;
        if ('' !== $fragment) {
            $uri .= '#' . $fragment;
        }

        return $this->uri = $uri;
    }

    public function getAuthority(): string
    {
        if (null !== $this->authority) {
            return $this->authority;
        }

        $this->authority = '' !== $this->userInfo ?
            $this->userInfo . '@' . $this->host :
            $this->host;

        if (null === $this->port) {
            return $this->authority;
        }

        if ($this->isNonStandardPort()) {
            return $this->authority .= ':' . $this->port;
        }

        return $this->authority;
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
        $fragment = $this->sanitizeFragment($fragment);
        if ($this->fragment === $fragment) {
            return $this;
        }

        $clone = clone $this;
        $clone->fragment = $fragment;

        return $clone;
    }

    public function withHost(string $host): UriInterface
    {
        $host = $this->sanitizeHost($host);
        if ($this->host === $host) {
            return $this;
        }

        $clone = clone $this;
        $clone->authority = null;
        $clone->host = $host;
        return $clone;
    }

    public function withPath(string $path): UriInterface
    {
        $path = $this->sanitizePath($path);
        if ($this->path === $path) {
            return $this;
        }

        $clone = clone $this;
        $clone->path = $path;
        return $clone;
    }

    public function withPort(?int $port): UriInterface
    {
        $sanitizedPort = $this->sanitizePort($port);
        if ($sanitizedPort === $this->port) {
            return $this;
        }

        $clone = clone $this;
        $clone->authority = null;
        $clone->port = $sanitizedPort;
        return $clone;
    }

    public function withQuery(string $query): UriInterface
    {
        $query = $this->sanitizeQuery($query);
        if ($this->query === $query) {
            return $this;
        }

        $copy = clone $this;
        $copy->query = $query;
        return $copy;
    }

    public function withScheme(string $scheme): UriInterface
    {
        $scheme = $this->sanitizeScheme($scheme);
        if ($this->scheme === $scheme) {
            return $this;
        }

        $copy = clone $this;
        $copy->scheme = $scheme;
        return $copy;
    }

    public function withUserInfo(string $user, ?string $password = null): UriInterface
    {
        $userInfo = $this->sanitizeUserInfo($user, $password);
        if ($this->userInfo === $userInfo) {
            return $this;
        }

        $copy = clone $this;
        $copy->authority = null;
        $copy->userInfo = $userInfo;
        return $copy;
    }

    /**
     * Percent encodes all reserved characters in the provided string according to the provided pattern. Characters that
     * are already encoded as a percentage will not be re-encoded.
     *
     * @link https://tools.ietf.org/html/rfc3986
     *
     * @psalm-suppress MixedArgument
     */
    private function encode(string $string, string $pattern): ?string
    {
        return preg_replace_callback(
            $pattern,
            static fn (array $matches): string => rawurlencode($matches[0]),
            $string
        );
    }

    /**
     * Is the given port a non-standard port for the current scheme.
     */
    private function isNonStandardPort(): bool
    {
        if ('' === $this->scheme) {
            return '' === $this->host || null !== $this->port;
        }

        if ('' === $this->host) {
            return false;
        }

        if (null === $this->port) {
            return false;
        }

        return ! array_key_exists($this->scheme, self::SCHEME_STANDARD_PORTS) ||
            $this->port !== self::SCHEME_STANDARD_PORTS[$this->scheme];
    }

    /**
     * Normalize string.
     */
    private function normalize(string $string): string
    {
        return strtr($string, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
    }

    /**
     * Sanitize the URI fragment.
     *
     * @throws InvalidArgumentException for invalid fragment strings
     */
    private function sanitizeFragment(string $fragment): string
    {
        if ('' === $fragment) {
            return $fragment;
        }

        if (str_starts_with($fragment, '#')) {
            $fragment = '%23' . substr($fragment, 1);
        }

        $result = $this->encode(
            $this->sanitizeInvalidUTF8Characters($fragment),
            '#(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/\?]+|%(?![A-Fa-f0-9]{2}))#u'
        );

        if (null === $result) {
            throw InvalidArgumentException::invalidFragment($fragment);
        }

        return $result;
    }

    /**
     * Sanitize the URI host.
     *
     * @throws InvalidArgumentException for invalid host
     */
    private function sanitizeHost(string $host): string
    {
        if ('' === $host) {
            return $host;
        }

        return $this->normalize($host);
    }

    /**
     * Percent-encode invalid UTF-8 characters in the provided string.
     *
     * Characters that are already encoded as a percentage will not be re-encoded.
     *
     * @link https://tools.ietf.org/html/rfc3986
     */
    private function sanitizeInvalidUTF8Characters(string $string): string
    {
        if ('' === $string) {
            return $string;
        }

        // check if given string contains only valid UTF-8 characters
        if (1 === preg_match('##u', $string)) {
            return $string;
        }

        return implode('', array_map(static function (string $letter): string {
            if (1 === preg_match('##u', $letter)) {
                return $letter;
            }

            return rawurlencode($letter);
        }, mb_str_split($string)));
    }

    /**
     * Sanitize the URI path.
     *
     * @throws InvalidArgumentException for invalid paths
     */
    private function sanitizePath(string $path): string
    {
        if ('' === $path) {
            return $path;
        }

        if (str_contains($path, '?')) {
            throw InvalidArgumentException::invalidPathContainsQueryString($path);
        }

        if (str_contains($path, '#')) {
            throw InvalidArgumentException::invalidPathContainsUriFragment($path);
        }

        $sanitizedPath = $this->encode(
            $this->sanitizeInvalidUTF8Characters($path),
            '#(?:[^' . self::CHAR_UNRESERVED . ')(:@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))#u'
        );

        if (null === $sanitizedPath) {
            throw InvalidArgumentException::invalidPath($path);
        }

        if ('' === $sanitizedPath) {
            // No path
            return $sanitizedPath;
        }

        if ('/' !== $sanitizedPath[0]) {
            // Relative path
            return $sanitizedPath;
        }
        // Only one leading slash, to prevent XSS attempts.
        return '/' . ltrim($sanitizedPath, '/');
    }

    /**
     * Sanitize the URI port.
     *
     * @throws InvalidArgumentException for invalid ports
     */
    private function sanitizePort(?int $port = null): ?int
    {
        if (null === $port) {
            return null;
        }

        if (self::PORT_MIN > $port) {
            throw InvalidArgumentException::invalidPort($port);
        }

        if (self::PORT_MAX < $port) {
            throw InvalidArgumentException::invalidPort($port);
        }

        return (self::SCHEME_STANDARD_PORTS[$this->scheme] ?? null) === $port ?
            null :
            $port;
    }

    /**
     * Sanitize the URI query string.
     *
     * @throws InvalidArgumentException for invalid query strings
     */
    private function sanitizeQuery(string $query): string
    {
        if ('' === $query) {
            return '';
        }

        if (str_contains($query, '#')) {
            throw InvalidArgumentException::invalidQueryContainsUriFragment($query);
        }

        if ('?' === $query) {
            return '';
        }

        if ('?' === $query[0]) {
            $query = ltrim($query, '?');
        }

        return implode('&', array_map(function (string $part): string {
            /** @var array{0:string, 1:null|string} $data */
            $data = explode('=', $part, 2);
            $data[1] ??= null;

            [$key, $value] = $data;
            if (null === $value) {
                return $this->sanitizeQueryStringKeyOrValue($key);
            }

            return $this->sanitizeQueryStringKeyOrValue($key) . '=' . $this->sanitizeQueryStringKeyOrValue($value);
        }, explode('&', $query)));
    }

    /**
     * Sanitize a query string key or value.
     *
     * @throws InvalidArgumentException for invalid query string key or value
     */
    private function sanitizeQueryStringKeyOrValue(string $keyOrValue): string
    {
        if ('' === $keyOrValue) {
            return $keyOrValue;
        }

        $sanitizedKeyOrValue = $this->encode(
            $this->sanitizeInvalidUTF8Characters($keyOrValue),
            '#(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/\?]+|%(?![A-Fa-f0-9]{2}))#u'
        );

        if (null === $sanitizedKeyOrValue) {
            throw InvalidArgumentException::invalidQueryStringKeyOrValue();
        }

        return $sanitizedKeyOrValue;
    }

    /**
     * Sanitize the URI scheme.
     *
     * @throws InvalidArgumentException for invalid or unsupported schemes
     */
    private function sanitizeScheme(string $scheme): string
    {
        if ('' === $scheme) {
            return $scheme;
        }

        $scheme = preg_replace('#:(//)?$#', '', $this->normalize($scheme));
        if (null === $scheme) {
            throw InvalidArgumentException::invalidScheme();
        }

        if ('' === $scheme) {
            return $scheme;
        }

        if (array_key_exists($scheme, self::SCHEME_STANDARD_PORTS)) {
            return $scheme;
        }

        throw InvalidArgumentException::unsupportedScheme($scheme, array_keys(self::SCHEME_STANDARD_PORTS));
    }

    /**
     * Sanitize the UserInfo.
     *
     * @throws InvalidArgumentException for invalid or unsupported schemes
     */
    private function sanitizeUserInfo(string $user, ?string $pass = null): string
    {
        if ('' === $user) {
            return $user;
        }

        $username = $this->encode(
            $this->sanitizeInvalidUTF8Characters($user),
            '#(?:[^%' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . ']+|%(?![A-Fa-f0-9]{2}))#u'
        );

        if (null === $username) {
            throw InvalidArgumentException::invalidUserInfo($user);
        }

        if (null === $pass) {
            return $username;
        }

        $password = $this->encode(
            $this->sanitizeInvalidUTF8Characters($pass),
            '#(?:[^%' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . ']+|%(?![A-Fa-f0-9]{2}))#u'
        );

        if (null === $password) {
            throw InvalidArgumentException::invalidUserInfo($user . ':' . $pass);
        }

        return $username . ':' . $password;
    }
}
