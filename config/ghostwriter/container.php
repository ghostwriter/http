<?php

declare(strict_types=1);

use Ghostwriter\Container\Interface\Service\DefinitionInterface;
use Ghostwriter\Container\Interface\Service\ExtensionInterface;
use Ghostwriter\Container\Interface\Service\FactoryInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * @return array{
 *     'alias': array<class-string,class-string>,
 *     'define': array<class-string,class-string<DefinitionInterface>>,
 *     'extend': array<class-string,list<class-string<ExtensionInterface>>>,
 *     'factory': array<class-string,class-string<FactoryInterface>>
 * }
 */
return [
    'alias' => [
        RequestFactoryInterface::class => \Laminas\Diactoros\RequestFactory::class,
        ResponseFactoryInterface::class => \Laminas\Diactoros\ResponseFactory::class,
        ServerRequestFactoryInterface::class => \Laminas\Diactoros\ServerRequestFactory::class,
        StreamFactoryInterface::class => \Laminas\Diactoros\StreamFactory::class,
        UploadedFileFactoryInterface::class => \Laminas\Diactoros\UploadedFileFactory::class,
        UriFactoryInterface::class => \Laminas\Diactoros\UriFactory::class,
        ClientInterface::class => \GuzzleHttp\Client::class,
    ],
    'define' => [],
    'extend' => [],
    'factory' => [],
];
