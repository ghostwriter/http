<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Tests\Unit\Client;

use Generator;
use Ghostwriter\Http\Client\Client;
use Ghostwriter\Http\Client\Exception\ClientException;
use Ghostwriter\Http\Client\Exception\NetworkException;
use Ghostwriter\Http\Client\Exception\RequestException;
use Ghostwriter\Http\Contract\Client\ClientInterface;
use Ghostwriter\Http\Contract\Client\Exception\ClientExceptionInterface;
use Ghostwriter\Http\Contract\Client\Exception\NetworkExceptionInterface;
use Ghostwriter\Http\Contract\Client\Exception\RequestExceptionInterface;
use Ghostwriter\Http\Factory\RequestFactory;
use Ghostwriter\Http\Factory\ResponseFactory;
use Ghostwriter\Http\Message\Request;
use Ghostwriter\Http\Message\Uri;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * @coversDefaultClass \Ghostwriter\Http\Client\Client
 *
 * @internal
 *
 * @small
 */
final class ClientTest extends TestCase
{
    /**
     * @return Generator<class-string<ClientExceptionInterface>, array<int, ClientExceptionInterface>>
     */
    public function clientExceptions(): Generator
    {
        $request = (new RequestFactory())->createRequest(Request::METHOD_GET, new Uri(''));
        yield from [
            ClientException::class => [new ClientException()],
            RequestException::class => [new RequestException($request, 'RequestException')],
            NetworkException::class => [new NetworkException($request, 'NetworkException')],
        ];
    }

    public function testClientMayChooseToAlterAReceivedHttpResponseBeforeReturningItToTheCallingLibrary(): void
    {
        $this->expectNotToPerformAssertions();
    }

    public function testClientMayChooseToSendAnAlteredHttpRequestFromTheOneItWasProvided(): void
    {
        $this->expectNotToPerformAssertions();
    }

    /**
     * @covers \Ghostwriter\Http\Client\Exception\ClientException::__construct
     * @covers \Ghostwriter\Http\Client\Exception\NetworkException::__construct
     * @covers \Ghostwriter\Http\Client\Exception\RequestException::__construct
     *
     * @dataProvider clientExceptions
     */
    public function testClientMayThrowMoreSpecificExceptionsProvidedTheyImplementClientExceptionInterface(
        Throwable $throwable
    ): void {
        self::assertInstanceOf(ClientExceptionInterface::class, $throwable);
    }

    public function testClientMustNotTreatAWellFormedHttpRequestOrHttpResponseAsAnErrorConditionResponseStatusCodesInThe400And500RangeMustBeReturnedToTheCallingLibraryAsNormal(): void
    {
        $this->expectNotToPerformAssertions();
    }

    public function testClientMustNotTreatAWellFormedHttpRequestOrHttpResponseAsAnErrorConditionResponseStatusCodesInThe400And500RangeMustNotCauseAnException(): void
    {
        $this->expectNotToPerformAssertions();
    }

    public function testClientMustReassembleAMultiStepHTTP1XXResponseItselfSoThatWhatIsReturnedToTheCallingLibraryIsAValidHttpResponseOfStatusCode200OrHigher(): void
    {
        $this->expectNotToPerformAssertions();
    }

    /** @covers ClientException::__construct */
    public function testClientMustThrowAnInstanceOfClientExceptionInterfaceIfItIsUnableToSendTheHttpRequestAtAll(): void
    {
        $this->expectException(ClientExceptionInterface::class);
        throw new ClientException();
    }

    /** @covers ClientException::__construct */
    public function testClientMustThrowAnInstanceOfClientExceptionInterfaceIfTheHttpResponseCouldNotBeParsedIntoAPsr7ResponseObject(): void
    {
        $this->expectException(ClientExceptionInterface::class);
        throw new ClientException();
    }

    /** @covers NetworkException::__construct */
    public function testClientMustThrowAnInstanceOfNetworkExceptionInterfaceIfTheRequestCannotBeSentDueToANetworkFailureOfAnyKindIncludingATimeout(): void
    {
        $this->expectException(NetworkExceptionInterface::class);
        throw new NetworkException(new Request(Request::METHOD_GET), __METHOD__);
    }

    /** @covers RequestException::__construct */
    public function testClientMustThrowAnInstanceOfRequestExceptionInterfaceIfARequestCannotBeSentBecauseTheRequestMessageIsMissingSomeCriticalPieceOfInformation(): void
    {
        // missing a Host or Method
        $this->expectException(RequestExceptionInterface::class);
        throw new RequestException(new Request(Request::METHOD_GET), __METHOD__);
    }

    /** @covers RequestException::__construct */
    public function testClientMustThrowAnInstanceOfRequestExceptionInterfaceIfARequestCannotBeSentBecauseTheRequestMessageIsNotAWellFormedHttpRequest(): void
    {
        $this->expectException(RequestExceptionInterface::class);
        throw new RequestException(new Request(Request::METHOD_GET), __METHOD__);
    }

    /**
     * @covers \Ghostwriter\Http\Client\Client::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(Client::class, $this->createClient());
        self::assertInstanceOf(ClientInterface::class, $this->createClient());
    }

    public function testGetHandle(): void
    {
        $this->expectNotToPerformAssertions();
    }

    public function testIfAClientChoosesToAlterEitherTheHttpRequestOrHttpResponseItMustEnsureThatTheObjectRemainsInternallyConsistent(): void
    {
        $this->expectNotToPerformAssertions();
    }

    public function testSendRequest(): void
    {
        $this->expectNotToPerformAssertions();
    }

    public function testSendRequests(): void
    {
        $this->expectNotToPerformAssertions();
    }

    public function testTheRequestObjectThatIsReturnedByAnExceptionMayBeADifferentObjectThanTheOnePassedToSendRequest(): void
    {
        $this->expectNotToPerformAssertions();
    }

    private function createClient(): ClientInterface
    {
        $responseFactory = new ResponseFactory();
        return new Client($responseFactory);
    }
}