<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Message;

use Ghostwriter\Http\Contract\Message\StreamInterface;
use Ghostwriter\Http\Contract\Message\UriInterface;

final class Request extends AbstractRequest
{
    protected StreamInterface $stream;

    /**
     * @param string                          $method   HTTP method
     * @param null|string|UriInterface        $uri      URI
     * @param array                           $headers  Request headers
     * @param resource|StreamInterface|string $body     Request body
     * @param string                          $protocol Protocol version
     */
    public function __construct(
        protected string $method = self::METHOD_GET,
        UriInterface|string|null $uri = null,
        protected array $headers = [],
        string $body = 'php://temp',
        protected string $protocol = '1.1'
    ) {
        if (! $uri instanceof UriInterface) {
            $uri = new Uri($uri ?? '');
        }

        $this->uri = $uri;

        // If body is empty, defer initialization of the stream until Request::getBody()
        if ('' === $body) {
            return;
        }

        $this->stream = Stream::create($body);
    }
}
