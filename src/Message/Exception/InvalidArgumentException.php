<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Message\Exception;

use Ghostwriter\Http\Contract\Message\Exception\MessageExceptionInterface;

final class InvalidArgumentException extends \InvalidArgumentException implements MessageExceptionInterface
{
    public static function invalidFragment(string $fragment): self
    {
        return new self(sprintf('Invalid fragment: "%s".', $fragment));
    }

    public static function invalidPath(string $path): self
    {
        return new self(sprintf('Invalid path: "%s".', $path));
    }

    public static function invalidPathContainsQueryString(string $path): self
    {
        return new self(sprintf('Invalid path: "%s"; Must not contain query string.', $path));
    }

    public static function invalidPathContainsUriFragment(string $path): self
    {
        return new self(sprintf('Invalid path: "%s"; Must not contain uri fragment.', $path));
    }

    public static function invalidPort(int $port): self
    {
        return new self(sprintf('Invalid port: "%d". Must be a valid TCP/UDP port between 0 and 65535', $port));
    }

    public static function invalidQuery(string $query): self
    {
        return new self(sprintf('Invalid query strings: "%s".', $query));
    }

    public static function invalidQueryContainsUriFragment(string $query): self
    {
        return new self(sprintf('Invalid query string: "%s"; Must not contain uri fragment.', $query));
    }

    public static function invalidQueryStringKeyOrValue(): self
    {
        return new self('Invalid query string key or value.');
    }

    public static function invalidScheme(): self
    {
        return new self('Invalid scheme.');
    }

    public static function invalidStreamCreateArgument(): self
    {
        return new self('First argument to Stream::create() must be a string, resource or StreamInterface.');
    }

    public static function invalidStreamProvided(): self
    {
        return new self('Invalid stream provided; must be a string stream identifier or stream resource.');
    }

    public static function invalidStreamResourceUri(string $resourceUri): self
    {
        return new self(sprintf('Invalid stream resource uri: "%s".', $resourceUri));
    }

    public static function invalidUri(string $uri): self
    {
        return new self(sprintf('Invalid uri: "%s".', $uri));
    }

    public static function invalidUserInfo(string $user): self
    {
        return new self(sprintf('Invalid user info: "%s".', $user));
    }

    /**
     * @param array<string> $allowedSchemes
     */
    public static function unsupportedScheme(string $scheme, array $allowedSchemes = []): self
    {
        return new self(sprintf(
            'Unsupported scheme "%s"; Must be any empty string or in the set ("%s")',
            $scheme,
            implode('", "', $allowedSchemes)
        ));
    }
}
