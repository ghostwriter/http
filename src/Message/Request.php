<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Message;

use Ghostwriter\Http\Contract\Message\RequestInterface;
use Ghostwriter\Http\Contract\Message\StreamInterface;
use Ghostwriter\Http\Contract\Message\UriInterface;
use Ghostwriter\Http\Message\Traits\RequestTrait;

final class Request implements RequestInterface
{
    use RequestTrait;

    protected StreamInterface $stream;

    /**
     * @param string                          $method   HTTP method
     * @param null|string|UriInterface        $uri      URI
     * @param array                           $headers  Request headers
     * @param resource|StreamInterface|string $body     Request body
     * @param string                          $protocol Protocol version
     */
    public function __construct(
        private string $method = self::METHOD_GET,
        private UriInterface|string|null $uri = null,
        private array $headers = [],
        private string $body = '',
        private string $protocol = '1.1'
    ) {
        if (! $uri instanceof UriInterface) {
            $this->uri = new Uri($uri ?? '');
        }

        // If body is empty, defer initialization of the stream until Request::getBody()
        if ('' === $body) {
            return;
        }

        $this->stream = Stream::create($body);
    }
}
