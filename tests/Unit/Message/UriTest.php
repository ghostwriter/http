<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Tests\Unit\Message;

use Ghostwriter\Http\Contract\Message\UriInterface;
use Ghostwriter\Http\Message\Exception\InvalidArgumentException;
use Ghostwriter\Http\Message\Uri;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

/**
 * @coversDefaultClass \Ghostwriter\Http\Message\Uri
 *
 * @small
 *
 * @internal
 */
final class UriTest extends TestCase
{
    private ?Uri $uri = null;

    public function createUri(string $uri = ''): Uri
    {
        return $this->uri = new Uri($uri);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     */
    public function test0AuthorityMustReturnAnEmptyStringIfNotPresent(): void
    {
        self::assertEmpty($this->createUri()->getAuthority());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::getPort
     * @covers \Ghostwriter\Http\Message\Uri::isNonStandardPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withPort
     */
    public function test0AuthorityShouldNotIncludePortIfNotPresent(): void
    {
        $emptyUri = $this->createUri();
        self::assertNull($emptyUri->getPort());
        self::assertEmpty($emptyUri->getAuthority());

        $uri = $this->createUri('http://example.com');
        self::assertNull($uri->getPort());
        self::assertSame('example.com', $uri->getAuthority());

        $uriWithPort = $uri->withPort(8080);
        self::assertSame(8080, $uriWithPort->getPort());
        self::assertSame('example.com:8080', $uriWithPort->getAuthority());

        $uriWithoutPort = $uriWithPort->withPort(null);
        self::assertNull($uriWithoutPort->getPort());
        self::assertSame('example.com', $uriWithoutPort->getAuthority());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::getPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withHost
     * @covers \Ghostwriter\Http\Message\Uri::withPort
     * @covers \Ghostwriter\Http\Message\Uri::withScheme
     *
     * @dataProvider standardSchemePortCombinations
     */
    public function test0AuthorityShouldNotIncludeStandardPortForTheCurrentScheme(string $scheme, int $port): void
    {
        $uri = $this->createUri()
            ->withHost('example.com')
            ->withScheme($scheme)
            ->withPort($port);

        self::assertNull($uri->getPort());
        self::assertSame('example.com', $uri->getAuthority());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getHost
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withHost
     */
    public function test0HostMustBeNormalizedToLowercase(): void
    {
        $uri = $this->createUri('HTTP://EXAMPLE.COM');
        self::assertSame('example.com', $uri->getHost());

        $new = $uri->withHost('EXAMPLE.ORG');
        self::assertSame('example.org', $new->getHost());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getHost
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function test0HostMustReturnAnEmptyStringIfNotPresent(): void
    {
        self::assertEmpty($this->createUri()->getHost());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getPath
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function test0PathCanBeAbsoluteStartingWithSlash(): void
    {
        self::assertSame('/absolute/path', $this->createUri('/absolute/path')->getPath());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getPath
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function test0PathCanBeEmpty(): void
    {
        self::assertEmpty($this->createUri()->getPath());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getPath
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function test0PathCanBeRootlessStartingWithoutSlash(): void
    {
        self::assertSame('rootless/path', $this->createUri('rootless/path')->getPath());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getPath
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     *
     * @dataProvider utf8PathsDataProvider
     */
    public function test0PathMustBePercentEncodedButMustNotDoubleEncodeAnyCharacters(
        string $url,
        string $expected
    ): void {
        self::assertSame($expected, $this->createUri($url)->getPath());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getPort
     * @covers \Ghostwriter\Http\Message\Uri::getScheme
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function test0PortMustReturnAnIntegerIfPortIsPresentAndItIsTheNonStandardPortForTheCurrentScheme(): void
    {
        $uri = $this->createUri('https://example.com:4433');
        self::assertSame('https', $uri->getScheme());
        self::assertSame(4433, $uri->getPort());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function test0PortMustReturnNullIfPortIsNotPresentAndSchemeIsNotPresent(): void
    {
        self::assertNull($this->createUri()->getPort());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getPort
     * @covers \Ghostwriter\Http\Message\Uri::getScheme
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function test0PortShouldReturnNullIfPortIsNotPresentButSchemeIsPresent(): void
    {
        $uri = $this->createUri('https://example.com');
        self::assertSame('https', $uri->getScheme());
        self::assertNull($uri->getPort());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getPort
     * @covers \Ghostwriter\Http\Message\Uri::getScheme
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function test0PortShouldReturnNullIfPortIsPresentAndItIsTheStandardPortForTheCurrentScheme(): void
    {
        $uri = $this->createUri('https://example.com:443');
        self::assertSame('https', $uri->getScheme());
        self::assertNull($uri->getPort());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getQuery
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     *
     * @dataProvider getQueries
     */
    public function test0Query(UriInterface $uri, string $expected): void
    {
        self::assertSame($expected, $uri->getQuery());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getQuery
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withQuery
     *
     * @dataProvider queryStringsForEncoding
     */
    public function test0QueryMustBePercentEncodedButMustNotDoubleEncodeAnyCharacters(
        string $query,
        string $expected
    ): void {
        $uri = $this->createUri()
            ->withQuery($expected);
        self::assertSame($expected, $uri->getQuery());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     */
    public function test0QueryMustNotContainALeadingQuestionMarkCharacter(): void
    {
        $this->expectNotToPerformAssertions();
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getPath
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function test0QueryMustReturnAnEmptyStringIfNotPresent(): void
    {
        self::assertEmpty($this->createUri()->getPath());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getScheme
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withScheme
     */
    public function test0SchemeMustBeNormalizedToLowercase(): void
    {
        $uri = $this->createUri('HTTP://EXAMPLE.COM');
        self::assertSame('http', $uri->getScheme());
        self::assertSame('https', $uri->withScheme('HTTPS')->getScheme());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getScheme
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withScheme
     */
    public function test0SchemeMustNotContainATrailingColonCharacter(): void
    {
        $uri = $this->createUri('https://example.com');
        self::assertStringNotContainsString(':', $uri->getScheme());

        // Strips Off Delimiter
        $new = $uri->withScheme('://');
        self::assertSame('', $new->getScheme());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getScheme
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withScheme
     */
    public function test0SchemeMustReturnAnEmptyStringIfNotPresent(): void
    {
        $uri = $this->createUri();
        self::assertEmpty($uri->getScheme());

        $uri = $this->createUri('https://example.com');
        self::assertEmpty($uri->withScheme('')->getScheme());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::isNonStandardPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withFragment
     * @covers \Ghostwriter\Http\Message\Uri::withHost
     * @covers \Ghostwriter\Http\Message\Uri::withPath
     * @covers \Ghostwriter\Http\Message\Uri::withPort
     * @covers \Ghostwriter\Http\Message\Uri::withQuery
     * @covers \Ghostwriter\Http\Message\Uri::withScheme
     * @covers \Ghostwriter\Http\Message\Uri::withUserInfo
     */
    public function test0UriToString(): void
    {
        self::assertSame(
            'https://0:0@0:0/0?0=0#0',
            (string) $this->createUri()
                ->withHost('0')
                ->withPort(0)
                ->withUserInfo('0', '0')
                ->withScheme('https')
                ->withPath('/0')
                ->withQuery('0=0')
                ->withFragment('0')
        );

        self::assertSame(
            'https://1:1@1:1/1?1#1',
            (string) $this->createUri()
                ->withHost('1')
                ->withPort(1)
                ->withUserInfo('1', '1')
                ->withScheme('https')
                ->withPath('/1')
                ->withQuery('1')
                ->withFragment('1')
        );
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function test0UserInfoMustNotContainATrailingAtCharacter(): void
    {
        $guest = $this->createUri('https://user:pass@example.com');
        self::assertStringEndsNotWith('@', $guest->getUserInfo());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withUserInfo
     */
    public function test0UserInfoMustReturnAnEmptyStringIfUsernameIsNotPresent(): void
    {
        $uri = $this->createUri();
        self::assertEmpty($uri->getUserInfo());

        $uri = $this->createUri('https://user:pass@example.com');
        self::assertSame('user:pass', $uri->getUserInfo());
        self::assertEmpty($uri->withUserInfo('')->getUserInfo());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withUserInfo
     */
    public function test0UserInfoMustReturnColonSeparatedUsernameAndPasswordIfPasswordIsPresent(): void
    {
        $user = $this->createUri('https://user:pass@example.com');
        self::assertStringContainsString(':', $user->getUserInfo());
        self::assertSame('user:pass', $user->getUserInfo());

        $uri = $user->withUserInfo('admin', 'secrete');
        self::assertStringContainsString(':', $uri->getUserInfo());
        self::assertSame('admin:secrete', $uri->getUserInfo());

        $guest = $uri->withUserInfo('guest');
        self::assertStringNotContainsString(':', $guest->getUserInfo());
        self::assertSame('guest', $guest->getUserInfo());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withUserInfo
     */
    public function test0UserInfoMustReturnUsernameIfPresent(): void
    {
        $uri = $this->createUri();
        self::assertEmpty($uri->getUserInfo());

        $admin = $uri->withUserInfo('admin');
        self::assertSame('admin', $admin->getUserInfo());

        $guest = $uri->withUserInfo('');
        self::assertEmpty($guest->getUserInfo());

        $user = $this->createUri('https://user:pass@example.com');
        self::assertSame('user:pass', $user->getUserInfo());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::isNonStandardPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function testAuthority(): void
    {
        $uri = $this->createUri('/');
        self::assertSame('', $uri->getAuthority());

        $uri = $this->createUri('http://test@example.com:80/');
        self::assertSame('test@example.com', $uri->getAuthority());

        $uri = $this->createUri('http://test@example.com:81/');
        self::assertSame('test@example.com:81', $uri->getAuthority());

        $uri = $this->createUri('http://user:test@example.com/');
        self::assertSame('user:test@example.com', $uri->getAuthority());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::getFragment
     * @covers \Ghostwriter\Http\Message\Uri::getHost
     * @covers \Ghostwriter\Http\Message\Uri::getPath
     * @covers \Ghostwriter\Http\Message\Uri::getPort
     * @covers \Ghostwriter\Http\Message\Uri::getQuery
     * @covers \Ghostwriter\Http\Message\Uri::getScheme
     * @covers \Ghostwriter\Http\Message\Uri::getUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::isNonStandardPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function testConstruct(): void
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        self::assertInstanceOf(UriInterface::class, $uri);
        self::assertSame('https', $uri->getScheme());
        self::assertSame('user:pass', $uri->getUserInfo());
        self::assertSame('local.example.com', $uri->getHost());
        self::assertSame(3001, $uri->getPort());
        self::assertSame('user:pass@local.example.com:3001', $uri->getAuthority());
        self::assertSame('/foo', $uri->getPath());
        self::assertSame('bar=baz', $uri->getQuery());
        self::assertSame('quz', $uri->getFragment());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Exception\InvalidArgumentException::invalidUri
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function testConstructorRaisesExceptionForSeriouslyMalformedURI(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->createUri('http:///www.example.org/');
    }

    /**
     * @covers \Ghostwriter\Http\Message\Exception\InvalidArgumentException::unsupportedScheme
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     *
     * @dataProvider invalidSchemes
     */
    public function testConstructWithUnsupportedSchemeRaisesAnException(string $scheme): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported scheme');

        $this->createUri($scheme . '://example.com');
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::getFragment
     * @covers \Ghostwriter\Http\Message\Uri::getHost
     * @covers \Ghostwriter\Http\Message\Uri::getPath
     * @covers \Ghostwriter\Http\Message\Uri::getPort
     * @covers \Ghostwriter\Http\Message\Uri::getQuery
     * @covers \Ghostwriter\Http\Message\Uri::getScheme
     * @covers \Ghostwriter\Http\Message\Uri::getUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function testEmptyUriConstruct(): void
    {
        $uri = new Uri();
        self::assertEmpty($uri->getScheme());
        self::assertEmpty($uri->getAuthority());
        self::assertEmpty($uri->getUserInfo());
        self::assertEmpty($uri->getHost());
        self::assertNull($uri->getPort());
        self::assertEmpty($uri->getPath());
        self::assertEmpty($uri->getQuery());
        self::assertEmpty($uri->getFragment());
        self::assertEmpty($uri->__toString());
        self::assertInstanceOf(UriInterface::class, $uri);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getFragment
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withFragment
     */
    public function testEncodeFragmentPrefixIfPresent(): void
    {
        $uri = new Uri('http://example.com');
        $new = $uri->withFragment('#/foo/bar');
        self::assertSame('%23/foo/bar', $new->getFragment());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getFragment
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     *
     * @dataProvider getFragments
     */
    public function testFragment(UriInterface $uri, string $expected): void
    {
        self::assertSame($expected, $uri->getFragment());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getFragment
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withFragment
     */
    public function testFragmentIsNotDoubleEncoded(): void
    {
        $expected = '/p%5Eth?key%5E=%60bar%23b@z';
        $uri      = (new Uri())->withFragment($expected);
        self::assertSame($expected, $uri->getFragment());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getFragment
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withFragment
     */
    public function testFragmentIsProperlyEncoded(): void
    {
        $uri      = (new Uri())->withFragment('/p^th?key^=`bar#b@z');
        $expected = '/p%5Eth?key%5E=%60bar%23b@z';
        self::assertSame($expected, $uri->getFragment());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getHost
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function testHost(): void
    {
        $uri = $this->createUri('/');
        self::assertSame('', $uri->getHost());

        $uri = $this->createUri('http://www.example.com/');
        self::assertSame('www.example.com', $uri->getHost());

        $uri = $this->createUri('HTTP://EXAMPLE.COM/');
        self::assertSame('example.com', $uri->getHost());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getScheme
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withScheme
     */
    public function testMutatingSchemeStripsOffDelimiter(): void
    {
        $uri = new Uri('http://example.com');
        $new = $uri->withScheme('https://');
        self::assertSame('https', $new->getScheme());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Exception\InvalidArgumentException::unsupportedScheme
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withScheme
     *
     * @dataProvider invalidSchemes
     *
     * @param non-empty-string $scheme
     */
    public function testMutatingWithUnsupportedSchemeRaisesAnException(string $scheme): void
    {
        $uri = new Uri('http://example.com');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported scheme');

        $uri->withScheme($scheme);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withFragment
     * @covers \Ghostwriter\Http\Message\Uri::withHost
     * @covers \Ghostwriter\Http\Message\Uri::withPath
     * @covers \Ghostwriter\Http\Message\Uri::withPort
     * @covers \Ghostwriter\Http\Message\Uri::withQuery
     * @covers \Ghostwriter\Http\Message\Uri::withScheme
     * @covers \Ghostwriter\Http\Message\Uri::withUserInfo
     *
     * @dataProvider mutations
     *
     * @param 'withFragment'|'withHost'|'withPath'|'withPort'|'withQuery'|'withScheme'|'withUserInfo' $method
     */
    public function testMutationResetsUriStringPropertyInClone(string $method, string|int $value): void
    {
        $uri    = new Uri('http://example.com/path?query=string#fragment');
        $string = (string) $uri;

        $r = new ReflectionObject($uri);
        $reflectionProperty = $r->getProperty('uri');
        $reflectionProperty->setAccessible(true);
        self::assertSame($string, $reflectionProperty->getValue($uri));

        $test = $uri->{$method}($value);
        $r2   = new ReflectionObject($uri);
        $p2   = $r2->getProperty('uri');
        $p2->setAccessible(true);
        self::assertNull($p2->getValue($test));

        self::assertSame($string, $reflectionProperty->getValue($uri));
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getPath
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     *
     * @dataProvider getPaths
     */
    public function testPath(UriInterface $uri, string $expected): void
    {
        self::assertSame($expected, $uri->getPath());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getPath
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withPath
     */
    public function testPathIsNotPrefixedWithSlashIfSetWithoutOne(): void
    {
        $uri = new Uri('http://example.com');
        $new = $uri->withPath('foo/bar');
        self::assertSame('foo/bar', $new->getPath());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::getFragment
     * @covers \Ghostwriter\Http\Message\Uri::getPath
     * @covers \Ghostwriter\Http\Message\Uri::getQuery
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withFragment
     * @covers \Ghostwriter\Http\Message\Uri::withPath
     * @covers \Ghostwriter\Http\Message\Uri::withQuery
     */
    public function testPercentageEncodedWillNotBeReEncoded(): void
    {
        $uri = $this->createUri('https://example.com/pa<th/to/tar>get/?qu^ery=str|ing#frag%ment');
        self::assertSame('https://example.com/pa%3Cth/to/tar%3Eget/?qu%5Eery=str%7Cing#frag%25ment', (string) $uri);

        $newUri = $this->createUri((string) $uri);
        self::assertSame((string) $uri, (string) $newUri);

        $uri = $this->createUri($path = '/pa%3C%3Eth')
            ->withPath($path);
        self::assertSame($path, $uri->getPath());

        $uri = $this->createUri('?' . $query = 'que%3C%3Ery=str%7Cing')->withQuery($query);
        self::assertSame($query, $uri->getQuery());

        $uri = $this->createUri('#' . $fragment = 'frag%3C%3Ement')->withFragment($fragment);
        self::assertSame($fragment, $uri->getFragment());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function testPort(): void
    {
        $uri = $this->createUri('http://www.example.com/');
        self::assertNull($uri->getPort());

        $uri = $this->createUri('http://www.example.com:80/');
        self::assertNull($uri->getPort());

        $uri = $this->createUri('https://www.example.com:443/');
        self::assertNull($uri->getPort());

        $uri = $this->createUri('http://www.example.com:81/');
        self::assertSame(81, $uri->getPort());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function testProperlyTrimsLeadingSlashesToPreventXSS(): void
    {
        self::assertSame('http://example.org/example.com', (string) new Uri('http://example.org//example.com'));
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withHost
     * @covers \Ghostwriter\Http\Message\Uri::withPath
     * @covers \Ghostwriter\Http\Message\Uri::withScheme
     */
    public function testReservedCharsInPathNotEncoded(): void
    {
        self::assertStringContainsString(
            '/v1/people/~:(first-name,last-name,email-address,picture-url)',
            (string) $this->createUri()
                ->withScheme('https')
                ->withHost('api.example.com')
                ->withPath('/v1/people/~:(first-name,last-name,email-address,picture-url)')
        );
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::isNonStandardPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     *
     * @dataProvider authorityInfo
     *
     * @param non-empty-string $url
     * @param non-empty-string $expected
     */
    public function testRetrievingAuthorityReturnsExpectedValues(string $url, string $expected): void
    {
        $uri = new Uri($url);
        self::assertSame($expected, $uri->getAuthority());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getScheme
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withScheme
     */
    public function testScheme(): void
    {
        $uri = $this->createUri('/');
        self::assertSame('', $uri->getScheme());

        $uri = $this->createUri('https://example.com/');
        self::assertSame('https', $uri->getScheme());

        $newUri = $uri->withScheme('http');
        self::assertNotSame($uri, $newUri);
        self::assertSame('http', $newUri->getScheme());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getPath
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withPath
     */
    public function testSettingEmptyPathOnAbsoluteUriReturnsAnEmptyPath(): void
    {
        $uri = new Uri('http://example.com/foo');
        $new = $uri->withPath('');
        self::assertSame('', $new->getPath());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function testStringRepresentationOfAbsoluteUriWithNoPathSetsAnEmptyPath(): void
    {
        $uri = new Uri('http://example.com');
        self::assertSame('http://example.com', (string) $uri);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function testStringRepresentationOfOriginFormWithNoPathRetainsEmptyPath(): void
    {
        $uri = new Uri('?foo=bar');
        self::assertSame('?foo=bar', (string) $uri);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getQuery
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withQuery
     */
    public function testStripsQueryPrefixIfPresent(): void
    {
        $new = $this->createUri()
            ->withQuery('?foo=bar');
        self::assertSame('foo=bar', $new->getQuery());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::isNonStandardPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function testUriDistinguishZeroFromEmptyString(): void
    {
        $expected = 'https://0:0@0:1/0?0#0';
        $uri      = new Uri($expected);
        self::assertSame($expected, (string) $uri);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withHost
     */
    public function testUriDoesNotAppendColonToHostIfPortIsEmpty(): void
    {
        $uri = (new Uri())->withHost('google.com');
        self::assertSame('//google.com', (string) $uri);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withHost
     */
    public function testUriToStringAuthorityIsPrefixedByDoubleSlashIfPresent(): void
    {
        self::assertSame('//example.org', (string) $this->createUri() ->withHost('example.org'));
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function testUserInfo(): void
    {
        $uri = $this->createUri('/');
        self::assertSame('', $uri->getUserInfo());

        $uri = $this->createUri('https://user:test@example.com/');
        self::assertSame('user:test', $uri->getUserInfo());

        $uri = $this->createUri('https://test@example.com/');
        self::assertSame('test', $uri->getUserInfo());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getQuery
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     *
     * @dataProvider utf8QueryStringsDataProvider
     */
    public function testUtf8Query(string $url, string $result): void
    {
        self::assertSame($result, $this->createUri($url)->getQuery());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getHost
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function testUtf8Uri(): void
    {
        $uri = new Uri('https://ኢትዮጵያ.et/');

        self::assertSame('ኢትዮጵያ.et', $uri->getHost());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::getFragment
     * @covers \Ghostwriter\Http\Message\Uri::isNonStandardPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withFragment
     */
    public function testWithFragmentReturnsNewInstanceWithProvidedFragment(): void
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withFragment('qat');
        self::assertNotSame($uri, $new);
        self::assertSame('qat', $new->getFragment());
        self::assertSame('https://user:pass@local.example.com:3001/foo?bar=baz#qat', (string) $new);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::getFragment
     * @covers \Ghostwriter\Http\Message\Uri::isNonStandardPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withFragment
     */
    public function testWithFragmentReturnsSameInstanceWithProvidedFragmentSameAsBefore(): void
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withFragment('quz');
        self::assertSame($uri, $new);
        self::assertSame('quz', $new->getFragment());
        self::assertSame('https://user:pass@local.example.com:3001/foo?bar=baz#quz', (string) $new);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::getHost
     * @covers \Ghostwriter\Http\Message\Uri::isNonStandardPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withHost
     */
    public function testWithHostReturnsNewInstanceWithProvidedHost(): void
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withHost('example.org');
        self::assertNotSame($uri, $new);
        self::assertSame('example.org', $new->getHost());
        self::assertSame('https://user:pass@example.org:3001/foo?bar=baz#quz', (string) $new);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::getHost
     * @covers \Ghostwriter\Http\Message\Uri::isNonStandardPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withHost
     */
    public function testWithHostReturnsSameInstanceWithProvidedHostIsSameAsBefore(): void
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withHost('local.example.com');
        self::assertSame($uri, $new);
        self::assertSame('local.example.com', $new->getHost());
        self::assertSame('https://user:pass@local.example.com:3001/foo?bar=baz#quz', (string) $new);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Exception\InvalidArgumentException::invalidPathContainsQueryString
     * @covers \Ghostwriter\Http\Message\Exception\InvalidArgumentException::invalidPathContainsUriFragment
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withPath
     *
     * @dataProvider invalidPaths
     */
    public function testWithPathRaisesExceptionForInvalidPaths(string $path): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid path');

        $this->createUri()
            ->withPath($path);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::getPath
     * @covers \Ghostwriter\Http\Message\Uri::isNonStandardPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withPath
     */
    public function testWithPathReturnsNewInstanceWithProvidedPath(): void
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withPath('/bar/baz');
        self::assertNotSame($uri, $new);
        self::assertSame('/bar/baz', $new->getPath());
        self::assertSame('https://user:pass@local.example.com:3001/bar/baz?bar=baz#quz', (string) $new);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::getPath
     * @covers \Ghostwriter\Http\Message\Uri::isNonStandardPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withPath
     */
    public function testWithPathReturnsSameInstanceWithProvidedPathSameAsBefore(): void
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withPath('/foo');
        self::assertSame($uri, $new);
        self::assertSame('/foo', $new->getPath());
        self::assertSame('https://user:pass@local.example.com:3001/foo?bar=baz#quz', (string) $new);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Exception\InvalidArgumentException::invalidPort
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withPort
     *
     * @dataProvider invalidPorts
     */
    public function testWithPortRaisesExceptionForInvalidPorts(?int $port): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(InvalidArgumentException::invalidPort($port)->getMessage());
        /** @psalm-suppress MixedArgument */
        $this->createUri()
            ->withPort($port);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::getPort
     * @covers \Ghostwriter\Http\Message\Uri::isNonStandardPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withPort
     *
     * @dataProvider validPorts
     */
    public function testWithPortReturnsNewInstanceWithProvidedPort(?int $port): void
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withPort($port);
        self::assertNotSame($uri, $new);
        self::assertSame($port, $new->getPort());
        self::assertSame(
            sprintf('https://user:pass@local.example.com%s/foo?bar=baz#quz', null === $port ? '' : ':' . $port),
            (string) $new
        );
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withPort
     */
    public function testWithPortReturnsSameInstanceWithProvidedPortIsSameAsBefore(): void
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withPort(3001);
        self::assertSame($uri, $new);
        self::assertSame(3001, $new->getPort());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Exception\InvalidArgumentException::invalidQueryContainsUriFragment
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withQuery
     *
     * @dataProvider invalidQueryStrings
     */
    public function testWithQueryRaisesExceptionForInvalidQueryStrings(string $query): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid query string');

        $this->createUri()
            ->withQuery($query);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::getQuery
     * @covers \Ghostwriter\Http\Message\Uri::isNonStandardPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withQuery
     */
    public function testWithQueryReturnsNewInstanceWithProvidedQuery(): void
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withQuery('baz=bat');
        self::assertNotSame($uri, $new);
        self::assertSame('baz=bat', $new->getQuery());
        self::assertSame('https://user:pass@local.example.com:3001/foo?baz=bat#quz', (string) $new);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::getPort
     * @covers \Ghostwriter\Http\Message\Uri::getScheme
     * @covers \Ghostwriter\Http\Message\Uri::isNonStandardPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withScheme
     */
    public function testWithSchemeReturnsNewInstanceWithNewScheme(): void
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        self::assertSame(3001, $uri->getPort());
        $new = $uri->withScheme('http');
        self::assertNotSame($uri, $new);
        self::assertSame('http', $new->getScheme());
        self::assertSame('http://user:pass@local.example.com:3001/foo?bar=baz#quz', (string) $new);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::getScheme
     * @covers \Ghostwriter\Http\Message\Uri::isNonStandardPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withScheme
     */
    public function testWithSchemeReturnsSameInstanceWithSameScheme(): void
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withScheme('https');
        self::assertSame($uri, $new);
        self::assertSame('https', $new->getScheme());
        self::assertSame('https://user:pass@local.example.com:3001/foo?bar=baz#quz', (string) $new);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withUserInfo
     *
     * @dataProvider userInfoProvider
     */
    public function testWithUserInfoEncodesUsernameAndPassword(
        string $user,
        string $password,
        string $expected
    ): void {
        self::assertSame($expected, $this->createUri() ->withUserInfo($user, $password) ->getUserInfo());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::getUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::isNonStandardPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withUserInfo
     */
    public function testWithUserInfoReturnsNewInstanceWithProvidedUser(): void
    {
        $new = $this->createUri()
            ->withUserInfo('ghostwriter');
        self::assertNotSame($this->uri, $new);
        self::assertSame('ghostwriter', $new->getUserInfo());
        self::assertSame('//ghostwriter@', (string) $new);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__clone
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::getUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::isNonStandardPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withUserInfo
     */
    public function testWithUserInfoReturnsNewInstanceWithProvidedUserAndPassword(): void
    {
        $new = $this->createUri()
            ->withUserInfo('ghostwriter', 'secret');
        self::assertNotSame($this->uri, $new);
        self::assertSame('ghostwriter:secret', $new->getUserInfo());
        self::assertSame('//ghostwriter:secret@', (string) $new);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::getUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::isNonStandardPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::withUserInfo
     */
    public function testWithUserInfoReturnsSameInstanceIfUserAndPasswordAreSameAsBefore(): void
    {
        $uri = new Uri('http://user:pass@example.com:8080/path/?query=string#fragment');
        $new = $uri->withUserInfo('user', 'pass');
        self::assertSame($uri, $new);
        self::assertSame('user:pass', $new->getUserInfo());
        self::assertSame('http://user:pass@example.com:8080/path/?query=string#fragment', (string) $new);
    }

   /**
    * @return non-empty-array<non-empty-string, array{non-empty-string, non-empty-string}>
    */
   private function authorityInfo(): array
   {
       return [
        'host-only'      => ['http://foo.com/bar', 'foo.com'],
           'host-port'      => ['http://foo.com:3000/bar', 'foo.com:3000'],
           'user-host'      => ['http://me@foo.com/bar', 'me@foo.com'],
           'user-host-port' => ['http://me@foo.com:3000/bar', 'me@foo.com:3000'],
    ];
   }

   /**
    * @return iterable<array-key, array<array-key, mixed>>
    */
   private function getFragments(): iterable
   {
       yield from [
           'empty' => [$this->createUri('http://www.example.com'), ''],
           'empty-hash' => [$this->createUri('http://www.example.com#'), ''],
           'foo' => [$this->createUri('http://www.example.com#foo'), 'foo'],
           //            'foo+bar' => [$this->createUri('http://www.example.com#foo+bar'), 'foo+bar'],
           'foo%20bar' => [$this->createUri('http://www.example.com#foo%20bar'), 'foo%20bar'],
       ];
   }

   /**
    * @return iterable<string, array<string|UriInterface>>
    */
   private function getPaths(): iterable
   {
       yield from [
           '/'=>[$this->createUri('http://www.example.com/'), '/'],
           'empty'=>[$this->createUri('http://www.example.com'), ''],
           'foo/bar'=>[$this->createUri('foo/bar'), 'foo/bar'],
           '/foo bar'=>[$this->createUri('http://www.example.com/foo bar'), '/foo%20bar'],
           '/foo%20bar'=>[$this->createUri('http://www.example.com/foo%20bar'), '/foo%20bar'],
           '/foo%2fbar'=>[$this->createUri('http://www.example.com/foo%2fbar'), '/foo%2fbar'],
       ];
   }

   /**
    * @return iterable<string, array<array-key, string|UriInterface>>
    */
   private function getQueries(): iterable
   {
       yield from [
           'no queries' => [$this->createUri('http://www.example.com'), ''],
           'empty query' => [$this->createUri('http://www.example.com?'), ''],
           '1 query' => [$this->createUri('http://www.example.com?foo=bar'), 'foo=bar'],
           'space queries' => [$this->createUri('http://www.example.com?foo=bar%26baz'), 'foo=bar%26baz'],
           '2 queries' => [$this->createUri('http://www.example.com?foo=bar&baz=biz'), 'foo=bar&baz=biz'],
       ];
   }

   /** @return iterable<string, array{string}> */
   private function invalidPaths(): array
   {
       return [
           'query'    => ['/bar/baz?bat=quz'],
           'fragment' => ['/bar/baz#bat'],
       ];
   }

   /** @return iterable<string, array{?int}> */
   private function invalidPorts(): iterable
   {
       yield from [
           'too-small' => [-1],
           'too-big'   => [65536],
       ];
   }

   /** @return iterable<string, array{iterable}> */
   private function invalidQueryStrings(): iterable
   {
       yield from [
           'fragment' => ['baz=bat#quz'],
       ];
   }

   /** @return iterable<string, array{iterable}> */
   private function invalidSchemes(): iterable
   {
       yield from [
           'mailto' => ['mailto'],
           'ftp'    => ['ftp'],
           'telnet' => ['telnet'],
           'ssh'    => ['ssh'],
           'git'    => ['git'],
       ];
   }

   /**
    * @return iterable<string, array{'withScheme'|'withUserInfo'|'withHost'|'withPort'|'withPath'|'withQuery'|'withFragment', non-empty-string|positive-int}>
    */
   private function mutations(): iterable
   {
       yield from [
           'scheme'    => ['withScheme', 'https'],
           'user-info' => ['withUserInfo', 'foo'],
           'host'      => ['withHost', 'www.example.com'],
           'port'      => ['withPort', 8080],
           'path'      => ['withPath', '/changed'],
           'query'     => ['withQuery', 'changed=value'],
           'fragment'  => ['withFragment', 'changed'],
       ];
   }

   /**
    * @return non-empty-array<non-empty-string, array{non-empty-string, non-empty-string}>
    */
   private function queryStringsForEncoding(): array
   {
       return [
           'key-only'        => ['k^ey', 'k%5Eey'],
           'key-value'       => ['k^ey=valu`', 'k%5Eey=valu%60'],
           'array-key-only'  => ['key[]', 'key%5B%5D'],
           'array-key-value' => ['key[]=valu`', 'key%5B%5D=valu%60'],
           'complex'         => ['k^ey&key[]=valu`&f<>=`bar', 'k%5Eey&key%5B%5D=valu%60&f%3C%3E=%60bar'],
       ];
   }

   /**
    * @return non-empty-array<non-empty-string, array{non-empty-string, positive-int}>
    */
   private function standardSchemePortCombinations(): array
   {
       return [
           'http'  => ['http', 80],
           'https' => ['https', 443],
       ];
   }

   /**
    * @return iterable<string, array{string, string, string}>
    */
   private function userInfoProvider(): iterable
   {
       // @codingStandardsIgnoreStart
       yield from [
           // name       => [ user,              credential, expected ]
           'valid-chars' => ['foo', 'bar', 'foo:bar'],
           'colon'       => ['foo:bar', 'baz:bat', 'foo%3Abar:baz%3Abat'],
           'at'          => ['user@example.com', 'cred@foo', 'user%40example.com:cred%40foo'],
           'percent'     => ['%25', '%25', '%25:%25'],
           'invalid-enc' => ['%ZZ', '%GG', '%25ZZ:%25GG'],
           'invalid-utf' => ["\x21\x92", '!?', '!%92:!%3F'],
       ];
       // @codingStandardsIgnoreEnd
   }

   /** @return iterable<array{string, string}> */
   private function utf8PathsDataProvider(): iterable
   {
       yield from [
           '/тестовый_путь/'=>[
               'http://example.com/тестовый_путь/',
               '/тестовый_путь/',
           ],
           '/ουτοπία/'=>['http://example.com/ουτοπία/', '/ουτοπία/'],
           '/ኢትዮጵያ/' =>  ['http://example.com/ኢትዮጵያ/', '/ኢትዮጵያ/'],
           '/%21%92' => ["http://example.com/\x21\x92", '/%21%92'],
           '/%21' => ['http://example.com/!?', '/%21'],
           '/foo^bar' =>  ['http://example.com/foo^bar', '/foo%5Ebar'],
           '/foo%5Ebar' =>  ['http://example.com/foo%5Ebar', '/foo%5Ebar'],
           'empty-path-on-origin-form-remains-an-empty-path' =>  ['?foo=bar', ''],
           'foo-bar1' =>  ['/foo/bar', '/foo/bar'],
           'foo-bar2' =>  ['foo/bar', 'foo/bar'],
           // 'foo-bar-NotSlashPrefixedIsEmittedWithSlashDelimiterWhenUriIsCastToString' =>  ['foo/bar', '/foo/bar'],
           '/valid///path' =>  ['http://example.org//valid///path', '/valid///path'],
           'ReservedCharsInPathNotEncoded' =>  [
               '/v1/people/~:(first-name,last-name,email-address,picture-url)',
               '/v1/people/~:(first-name,last-name,email-address,picture-url)',
           ],
       ];
   }

   /** @return non-empty-list<array{non-empty-string, non-empty-string}> */
   private function utf8QueryStringsDataProvider(): array
   {
       return [
           ['http://example.com/?q=ኢትዮጵያ', 'q=ኢትዮጵያ'],
           ["http://example.com/?q=\x21\x92", 'q=!%92'],
       ];
   }

   /** @return iterable<string, array<?int>> */
   private function validPorts(): iterable
   {
       yield from [
           'null'       => [null],
           'int'        => [42],
       ];
   }
}
