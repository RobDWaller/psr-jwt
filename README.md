# PSR Compliant JSON Web Token Middleware
[![Build Status](https://travis-ci.org/RobDWaller/psr-jwt.svg?branch=master)](https://travis-ci.org/RobDWaller/psr-jwt) [![codecov](https://codecov.io/gh/RobDWaller/psr-jwt/branch/master/graph/badge.svg)](https://codecov.io/gh/RobDWaller/psr-jwt) [![Infection MSI](https://badge.stryker-mutator.io/github.com/RobDWaller/psr-jwt/master)](https://infection.github.io) [![StyleCI](https://github.styleci.io/repos/167511682/shield?branch=master)](https://github.styleci.io/repos/167511682) [![Latest Stable Version](https://poser.pugx.org/rbdwllr/psr-jwt/v/stable)](https://packagist.org/packages/rbdwllr/psr-jwt) ![PHP Version Support](https://img.shields.io/travis/php-v/RobDWaller/psr-jwt/master)

PSR-JWT is a middleware library which allows you to authorise JSON Web Tokens contained in a web request. It is [PSR-7](https://www.php-fig.org/psr/psr-7/) and [PSR-15](https://www.php-fig.org/psr/psr-15/) compliant and built on top of [ReallySimpleJWT](https://github.com/RobDWaller/ReallySimpleJWT).

The library also allows you to generate JSON Web Tokens and the PSR-7 PSR-15 compliant middleware can be added to any compatible framework, such as [Slim PHP](http://www.slimframework.com/).

For more information on JSON Web Tokens please read [RFC 7519](https://tools.ietf.org/html/rfc7519). Also to learn more about how to pass JSON Web Tokens to web applications please read up on bearer token authorisation in [RFC 6750](https://tools.ietf.org/html/rfc6750).

## Contents

- [Setup](#setup)
- [Basic Usage](#basic-usage)
    - [Slim PHP Example Implementation](#slim-php-example-implementation)
    - [Generate JSON Web Token](#generate-json-web-token)
    - [Parse and Validate JSON Web Token](#parse-and-validate-json-web-token)
    - [Retrieve Token From the Request](retrieve-token-from-the-request)
- [Advanced Usage](#advanced-usage)
    - [Handlers](#handlers)
    - [Create Custom Handler](#create-custom-handler)

## Setup

To install this package you will need to install [Composer](https://getcomposer.org/) and then run `composer init`. Once this is done you can install the package via the command line or by editing the composer.json file created by the `composer init` command.

Finally you will need to reference the Composer autoloader in your PHP code, `require 'vendor/autoload.php';`. The location of the autoload file will differ dependent on where your code is run. Note, some frameworks already have the autoload file referenced for you.

**Install via Composer on the command line:**

```bash
composer require rbdwllr/psr-jwt
```

**Install via the composer.json file:**

```javascript
"require": {
    "rbdwllr/psr-jwt": "^0.3"
}
```

## Basic Usage

PsrJwt can be used with any PSR-7 / PSR-15 compliant framework. Just call one of the middleware factory methods and they will return a middleware instance that exposes two methods, `__invoke()` and `process()`. The latter will work with PSR-15 compliant frameworks and the former will work with older PSR-7 compliant frameworks.

```php
// Will generate a text/html response if JWT authorisation fails.
\PsrJwt\Factory\JwtMiddleware::html('secret', 'tokenKey', 'body');

// Will generate an application/json response if JWT authorisation fails.
\PsrJwt\Factory\JwtMiddleware::json('secret', 'tokenKey', ['body']);
```

**Secret:** is the string required to hash the JSON Web Token signature.

**Token Key:** is the key required to retrieve the JSON Web Token from a cookie, query parameter or the request body. By default though the library looks for tokens in the bearer field of the authorization header. If you use the bearer field you can pass an empty string for the token key `''`.

**Body:** is the body content you would like to return in the response if authorisation fails. For example, `<h1>Authorisation Failed!</h1>`.

### Slim PHP Example Implementation

To add the middleware to a route in Slim PHP you can use the code below.

```php
// Can be added to any routes file in Slim, often index.php.
require '../../vendor/autoload.php';

$app->get('/jwt', function (Request $request, Response $response) {
    $response->getBody()->write("JSON Web Token is Valid!");

    return $response;
})->add(\PsrJwt\Factory\JwtMiddleware::html('Secret123!456$', 'jwt', 'Authorisation Failed'));
```

### Generate a JSON Web Token

To generate JSON Web Tokens PsrJwt offers a wrapper for the library [ReallySimpleJWT](https://github.com/RobDWaller/ReallySimpleJWT). You can create an instance of the ReallySimpleJWT builder by calling the built in factory method.

```php
require 'vendor/autoload.php';

$factory = new \PsrJwt\Factory\Jwt();

$builder = $factory->builder();

$token = $builder->setSecret('!secReT$123*')
    ->setPayloadClaim('uid', 12)
    ->build();
```

### Parse and Validate JSON Web Token

If for some reason you need to parse or validate a token outside of the normal middleware authorisation flow the JWT factory class provides a parser method. 

This will return an instance of the Really Simple JWT Parse class which provides token parsing and validation functionality.

```php
require 'vendor/autoload.php';

$factory = new \PsrJwt\Factory\Jwt();

$parser = $factory->parse('token', 'secret');

$parser->validate();

$parser->parse();
```

For more information on creating, parsing and validating tokens please read the [ReallySimpleJWT](https://github.com/RobDWaller/ReallySimpleJWT/blob/master/readme.md) documentation.

### Retrieve Token From the Request

If you would like to retrieve the JSON Web Token from the request outside of the normal middleware authorisation flow you can use the request helper class. 

It allows you to retrive the token itself or just access the token's payload or header.

```php
require 'vendor/autoload.php';

use PsrJwt\Helper\Request;

$helper = new Request();

// Will return a ReallySimpleJWT Parsed object.
$helper->getParsedToken($request, $tokenKey);

// Return the token header as an array.
$helper->getTokenHeader($request, $tokenKey);

// Return the token payload as an array.
$helper->getTokenPayload($request, $tokenKey);
```

## Advanced Usage

You don't have to use the factory methods explained above to generate the JWT authorisation middleware you can instantiate all the required classes directly. This allows you to configure a custom setup.

```php
use PsrJwt\Handler\Html;
use PsrJwt\JwtAuthMiddleware;

$htmlHandler = new Html($secret, $tokenKey, $body);

$middleware = new JwtAuthMiddleware($htmlHandler);
```

### Handlers

PsrJwt is built to work with any PSR-15 compliant handler. As standard it comes with two built in handlers, one which returns text/html responses and another which returns application/json responses.

You can use these handlers simply by instantiating them and passing them to the PsrJwt middleware.

```php
// Create Middleware with JSON handler.
use PsrJwt\Handler\Json;
use PsrJwt\JwtAuthMiddleware;

// The handler.
$jsonHandler = new Json($secret, $tokenKey, $body);

// The middleware.
$middleware = new JwtAuthMiddleware($jsonHandler);
```

### Create Custom Handler

To create your own handler you need to do two things. First create a class which implements the `Psr\Http\Server\RequestHandlerInterface` [interface](https://www.php-fig.org/psr/psr-15/). This requires you create a `handle()` method which consumes a `Psr\Http\Message\ServerRequestInterface` object and returns a `Psr\Http\Message\ResponseInterface` object.

Next you will need to extend the `PsrJwt\Auth\Authorise` class as this will give you access to the JSON Web Token authorisation functionality. Once this is done you will be able to pass your handler to the `PsrJwt\JwtAuthMiddleware` class and then integrate it with your desired framework.

```php
// An example JWT Authorisation Handler.
use PsrJwt\Auth\Authorise;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Response;

class MyHandler extends Authorise implements RequestHandlerInterface
{
    public function __construct(string $secret, string $tokenKey)
    {
        parent::__construct($secret, $tokenKey);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $auth = $this->authorise($request);

        return new Response(
            $auth->getCode(),
            [],
            'The Response Body',
            '1.1',
            $auth->getMessage()
        );
    }
}
```

## License

MIT

## Author

Rob Waller

Twitter: [@robdwaller](https://twitter.com/RobDWaller)
