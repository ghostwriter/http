<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Client;

use CurlHandle;
use CurlMultiHandle;
use Ghostwriter\Http\Client\Exception\ClientException;
use Ghostwriter\Http\Client\Exception\NetworkException;
use Ghostwriter\Http\Client\Traits\ClientTrait;
use Ghostwriter\Http\Contract\Client\ClientInterface;
use Ghostwriter\Http\Contract\Factory\ResponseFactoryInterface;
use Ghostwriter\Http\Contract\Message\RequestInterface;
use Ghostwriter\Http\Contract\Message\ResponseInterface;

use const CURLINFO_HEADER_SIZE;
use const CURLOPT_CUSTOMREQUEST;
use const CURLOPT_HEADER;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_URL;
use function curl_close;
use function curl_errno;
use function curl_error;
use function curl_exec;
use function curl_getinfo;
use function curl_init;
use function curl_multi_add_handle;
use function curl_multi_close;

use function curl_multi_exec;
use function curl_multi_init;
use function curl_multi_remove_handle;
use function curl_setopt_array;
use function explode;
use function in_array;
use function ltrim;
use function sprintf;
use function substr;

final class Client implements ClientInterface
{
    use ClientTrait;

    /**
     * @param int[] $curlOptions
     */
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private array $curlOptions = []
    ) {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $curlHandle = $this->createCurlHandleFromRequest($request);

        $isSuccess = curl_exec($curlHandle);
        if (false === $isSuccess) {
            throw new NetworkException($request, curl_error($curlHandle), curl_errno($curlHandle));
        }

        $response = $this->createResponseFromCurlHandle($curlHandle);

        curl_close($curlHandle);

        return $response;
    }

    /**
     * Sends the given requests and returns responses in the same order.
     *
     * @throws NetworkException
     * @throws ClientException
     *
     * @return array<array-key, ResponseInterface>
     *
     *
     */
    public function sendRequests(array $requests = []): array
    {
        /** @var bool|CurlMultiHandle $curlMultiHandle */
        $curlMultiHandle = curl_multi_init();
        if (! $curlMultiHandle instanceof CurlMultiHandle) {
            throw ClientException::unableToCreateCurlMultiHandle();
        }

        $curlHandles = [];
        foreach ($requests as $i => $request) {
            $curlHandles[$i] = $this->createCurlHandleFromRequest($request);
            curl_multi_add_handle($curlMultiHandle, $curlHandles[$i]);
        }

        do {
            curl_multi_exec($curlMultiHandle, $isRunning);
        } while ($isRunning);

        $responses = [];
        foreach ($curlHandles as $i => $curlHandle) {
            $responses[$i] = $this->createResponseFromCurlHandle($curlHandle);
            curl_multi_remove_handle($curlMultiHandle, $curlHandle);
            curl_close($curlHandle);
        }

        curl_multi_close($curlMultiHandle);

        return $responses;
    }

    /**
     * Creates a CurlHandle from the given request.
     *
     * @throws ClientException
     */
    private function createCurlHandleFromRequest(RequestInterface $request): CurlHandle
    {
        $curlOptions = $this->curlOptions;

        $curlOptions[CURLOPT_RETURNTRANSFER] = true;
        $curlOptions[CURLOPT_HEADER] = true;

        $curlOptions[CURLOPT_CUSTOMREQUEST] = $request->getMethod();
        $curlOptions[CURLOPT_URL] = (string) $request->getUri();

        $curlOptions[CURLOPT_HTTPHEADER] = [];
        foreach ($request->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $curlOptions[CURLOPT_HTTPHEADER][] = sprintf('%s: %s', $name, $value);
            }
        }

        $curlOptions[CURLOPT_POSTFIELDS] = null;
        if (! in_array($request->getMethod(), ['GET', 'HEAD'], true)) {
            $curlOptions[CURLOPT_POSTFIELDS] = (string) $request->getBody();
        }

        $curlHandle = curl_init();

        $isSuccessful = curl_setopt_array($curlHandle, $curlOptions);
        if (! $isSuccessful) {
            throw ClientException::unableToConfigureCurlHandle();
        }

        return $curlHandle;
    }

    /**
     * Creates a response from the given CurlHandle.
     */
    private function createResponseFromCurlHandle(CurlHandle $curlHandle): ResponseInterface
    {
        /** @var array{http_code:int,total_time:float} $curlInfo */
        $curlInfo = curl_getinfo($curlHandle);

        $response = $this->responseFactory
            ->createResponse($curlInfo['http_code'])
            ->withAddedHeader('X-Request-Time', sprintf('%.3f ms', $curlInfo['total_time'] * 1000));

        /** @var ?string $message */
        $message = curl_multi_getcontent($curlHandle);
        if (null === $message) {
            return $response;
        }

        /** @var int $headerSize */
        $headerSize = curl_getinfo($curlHandle, CURLINFO_HEADER_SIZE);
        $header = substr($message, 0, $headerSize);
        $response = $this->populateResponseWithHeaderFields($response, $header);

        $body = substr($message, $headerSize);
        $response->getBody()
            ->write($body);

        return $response;
    }

    /**
     * Populates the given response with the given header's fields.
     *
     * @link https://datatracker.ietf.org/doc/html/rfc7230#section-3.2
     */
    private function populateResponseWithHeaderFields(ResponseInterface $response, string $header): ResponseInterface
    {
        $fields = explode("\r\n", $header);

        foreach ($fields as $i => $field) {
            // The first line of a response message is the status-line, consisting
            // of the protocol version, a space (SP), the status code, another
            // space, a possibly empty textual phrase describing the status code,
            // and ending with CRLF.
            // https://datatracker.ietf.org/doc/html/rfc7230#section-3.1.2
            if (0 === $i) {
                continue;
            }

            // All HTTP/1.1 messages consist of a start-line followed by a sequence
            // of octets in a format similar to the Internet Message Format:
            // zero or more header fields (collectively referred to as
            // the "headers" or the "header section"), an empty line indicating the
            // end of the header section, and an optional message body.
            // https://datatracker.ietf.org/doc/html/rfc7230#section-3
            // https://datatracker.ietf.org/doc/html/rfc5322
            if ('' === $field) {
                break;
            }

            // While HTTP/1.x used the message start-line (see [RFC7230],
            // Section 3.1) to convey the target URI, the method of the request, and
            // the status code for the response, HTTP/2 uses special pseudo-header
            // fields beginning with ':' character (ASCII 0x3a) for this purpose.
            // https://datatracker.ietf.org/doc/html/rfc7540#section-8.1.2.1
            if (':' === $field[0]) {
                continue;
            }

            [$name, $value] = explode(':', $field, 2);

            $response = $response->withAddedHeader($name, ltrim($value));
        }

        return $response;
    }
}
