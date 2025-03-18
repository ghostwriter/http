# Http

[![GitHub Sponsors](https://img.shields.io/github/sponsors/ghostwriter?label=Sponsor+@ghostwriter/http&logo=GitHub+Sponsors)](https://github.com/sponsors/ghostwriter)
[![Automation](https://github.com/ghostwriter/http/actions/workflows/automation.yml/badge.svg)](https://github.com/ghostwriter/http/actions/workflows/automation.yml)
[![Supported PHP Version](https://badgen.net/packagist/php/ghostwriter/http?color=8892bf)](https://www.php.net/supported-versions)
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

## RFC

- [RFC3864: Registration Procedures for Message Header Fields](https://datatracker.ietf.org/doc/html/rfc3864)
- [RFC5234: Augmented BNF for Syntax Specifications](https://datatracker.ietf.org/doc/html/rfc5234)
- [RFC9110: HTTP Semantics](https://datatracker.ietf.org/doc/html/rfc9110)
- [RFC9111: HTTP Caching](https://datatracker.ietf.org/doc/html/rfc9111)
- [RFC9112: HTTP/1.1](https://datatracker.ietf.org/doc/html/rfc9112)
- [RFC9113: HTTP/2](https://datatracker.ietf.org/doc/html/rfc9113)
- [RFC9114: HTTP/3](https://datatracker.ietf.org/doc/html/rfc9114)

## Usage

```php

$router =  Router::new();

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
    $router->get('/posts/{post}/{?slug}', PostShowHandler::class, 'members.show');
});

$router->middleware([AuthMiddleware::class], function($router){
    $router->get('/users', MembersIndexHandler::class, 'members.index');
    $router->get('/users/{member}', MemberShowHandler::class, 'members.show');

    $router->get('/posts/create', PostCreateHandler::class, 'members.create');
    $router->post('/posts', PostStoreHandler::class, 'members.store');
    $router->get('/posts/{post}/edit', PostEditHandler::class, 'members.edit');
    $router->put('/posts/{post}', PostUpdateHandler::class, 'members.update');
    $router->delete('/posts/{post}', PostDeleteHandler::class, 'members.delete');
});

$request =  ServerRequest::new();

$server =  Server::new($router); // RequestHandler

$server->handle($request); // Response

```

### Changelog

Please see [CHANGELOG.md](./CHANGELOG.md) for more information what has changed recently.

### Security

If you discover any security related issues, please email `nathanael.esayeas@protonmail.com` or create a [Security Advisory](https://github.com/ghostwriter/clock/security/advisories/new) instead of using the issue tracker.

## License

The BSD-4-Clause. Please see [License File](./LICENSE) for more information.
