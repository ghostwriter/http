<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Container;

use Ghostwriter\Container\Interface\Service\ProviderInterface;
use Ghostwriter\Container\Service\Provider\AbstractProvider;
use GuzzleHttp\Client;
use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UploadedFileFactory;
use Laminas\Diactoros\UriFactory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * @see HttpProviderTest
 */
final class HttpProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * alias => service.
     *
     * @var array<class-string,class-string>
     */
    public const array ALIAS = [
        RequestFactoryInterface::class => RequestFactory::class,
        ResponseFactoryInterface::class => ResponseFactory::class,
        ServerRequestFactoryInterface::class => ServerRequestFactory::class,
        StreamFactoryInterface::class => StreamFactory::class,
        UploadedFileFactoryInterface::class => UploadedFileFactory::class,
        UriFactoryInterface::class => UriFactory::class,
        ClientInterface::class => Client::class,
    ];
}
