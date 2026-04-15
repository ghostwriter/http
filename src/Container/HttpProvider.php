<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Container;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Ghostwriter\Container\Interface\BuilderInterface;
use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Service\Provider\AbstractProvider;
use Override;
use Throwable;

/**
 * @see HttpProviderTest
 */
final class HttpProvider extends AbstractProvider
{
    /**
     * @throws Throwable
     */
    #[Override]
    public function register(BuilderInterface $builder): void
    {
        $builder->alias(RequestFactoryInterface::class, \Laminas\Diactoros\RequestFactory::class);
        $builder->alias(ResponseFactoryInterface::class, \Laminas\Diactoros\ResponseFactory::class);
        $builder->alias(ServerRequestFactoryInterface::class, \Laminas\Diactoros\ServerRequestFactory::class);
        $builder->alias(StreamFactoryInterface::class, \Laminas\Diactoros\StreamFactory::class);
        $builder->alias(UploadedFileFactoryInterface::class, \Laminas\Diactoros\UploadedFileFactory::class);
        $builder->alias(UriFactoryInterface::class, \Laminas\Diactoros\UriFactory::class);
        $builder->alias(ClientInterface::class, \GuzzleHttp\Client::class);
    }
}
