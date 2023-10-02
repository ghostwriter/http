# HTTP

[![Compliance](https://github.com/ghostwriter/http/actions/workflows/compliance.yml/badge.svg)](https://github.com/ghostwriter/http/actions/workflows/compliance.yml)
[![Supported PHP Version](https://badgen.net/packagist/php/ghostwriter/http?color=8892bf)](https://www.php.net/supported-versions)
[![GitHub Sponsors](https://img.shields.io/github/sponsors/ghostwriter?label=Sponsor+@ghostwriter/http&logo=GitHub+Sponsors)](https://github.com/sponsors/ghostwriter)
[![Code Coverage](https://codecov.io/gh/ghostwriter/http/branch/main/graph/badge.svg)](https://codecov.io/gh/ghostwriter/http)
[![Type Coverage](https://shepherd.dev/github/ghostwriter/http/coverage.svg)](https://shepherd.dev/github/ghostwriter/http)
[![Latest Version on Packagist](https://badgen.net/packagist/v/ghostwriter/http)](https://packagist.org/packages/ghostwriter/http)
[![Downloads](https://badgen.net/packagist/dt/ghostwriter/http?color=blue)](https://packagist.org/packages/ghostwriter/http)

HTTP Client and Server abstraction for PHP.

> **Warning**
>
> This project is not finished yet, work in progress.

## Installation

You can install the package via composer:

``` bash
composer require ghostwriter/http
```

## Usage

```php

$router = new Router();

$router->addRoute('GET', '/', HomeHandler::class, [GuestMiddleware::class]);

$router->get('/about', AboutHandler::class, [GuestMiddleware::class]);

$router->get('/auth/github', GitHubLoginHandler::class, [GuestMiddleware::class], 'auth.login.github');

    // create, read, edit, update, store, delete, view, show 
$router->middleware([GuestMiddleware::class], function($router){
    $router->get('/auth/login', LoginCreateHandler::class, 'auth.login.create');
    $router->post('/auth/login', LoginStoreHandler::class, 'auth.login.store');

    $router->get('/auth/register', RegisterCreateHandler::class, 'auth.register.create');
    $router->post('/auth/register', RegisterStoreHandler::class, 'auth.register.store');

    $router->get('/posts', PostIndexHandler::class, 'members.index');
    $router->get('/posts/{post:id}', PostShowHandler::class, 'members.show');
});

$router->middleware([AuthMiddleware::class], function($router){
    $router->get('/users', MembersIndexHandler::class, 'members.index');
    $router->get('/users/{member:id}', MemberShowHandler::class, 'members.show');

    $router->get('/posts/create', PostCreateHandler::class, 'members.create');
    $router->post('/posts', PostStoreHandler::class, 'members.store');
    $router->get('/posts/{post:id}/edit', PostEditHandler::class, 'members.edit');
    $router->put('/posts/{post:id}', PostUpdateHandler::class, 'members.update');
    $router->delete('/posts/{post:id}', PostDeleteHandler::class, 'members.delete');
});

$request = new ServerRequest();

$server = new Server($router); // RequestHandler

$server->handle($request); // Response

```

### Changelog

Please see [CHANGELOG.md](./CHANGELOG.md) for more information what has changed recently.

### Security

If you discover any security related issues, please email `nathanael.esayeas@protonmail.com` or create a [Security Advisory](https://github.com/ghostwriter/clock/security/advisories/new) instead of using the issue tracker.

## License

The BSD-3-Clause. Please see [License File](./LICENSE) for more information.
