# PSR Compliant JSON Web Token Middleware
[![Build Status](https://travis-ci.org/RobDWaller/psr-jwt.svg?branch=master)](https://travis-ci.org/RobDWaller/psr-jwt) [![codecov](https://codecov.io/gh/RobDWaller/psr-jwt/branch/master/graph/badge.svg)](https://codecov.io/gh/RobDWaller/psr-jwt) [![Infection MSI](https://badge.stryker-mutator.io/github.com/RobDWaller/psr-jwt/master)](https://infection.github.io) [![StyleCI](https://github.styleci.io/repos/167511682/shield?branch=master)](https://github.styleci.io/repos/167511682) [![Latest Stable Version](https://poser.pugx.org/rbdwllr/psr-jwt/v/stable)](https://packagist.org/packages/rbdwllr/psr-jwt) ![PHP Version Support](https://img.shields.io/travis/php-v/RobDWaller/psr-jwt/master)

A [PSR-7](https://www.php-fig.org/psr/psr-7/) and [PSR-15](https://www.php-fig.org/psr/psr-15/) compliant JSON Web Token middleware library built on top of [ReallySimpleJWT](https://github.com/RobDWaller/ReallySimpleJWT).

The library allows you to create JSON Web Tokens and then validate them using PSR-15 compliant middleware which can be added to compatible frameworks such as [Slim PHP](http://www.slimframework.com/) and [Zend Expressive](https://docs.zendframework.com/zend-expressive/).

For more information on JSON Web Tokens please read [RFC 7519](https://tools.ietf.org/html/rfc7519). Also to learn more about how to pass JSON Web Tokens to web applications please read up on bearer token authorisation in [RFC 6750](https://tools.ietf.org/html/rfc6750).

## Contents

- [Setup](#setup)
- [Basic Usage](#basic-usage)
    - [Slim PHP Example Implementation](#slim-php-example-implementation)
    - [Zend Expressive Example Implementation](#zend-expressive-example-implementation)
    - [JSON Response Handler](#json-response-handler)
    - [Generate JSON Web Token](#generate-json-web-token)
- [Advanced Usage](#advanced-usage)
    - [Handlers](#handlers)
    - [Create Custom Handler](#create-custom-handler)

## Setup

To install this package you will need to install [Composer](https://getcomposer.org/) and then run `composer init`. Once this is done you can install the package via the command line or by editing the composer.json file created by the `composer init` command.

Finally you will need to reference the composer autoloader in your PHP code, `require 'vendor/autoload.php';`. The location of the autoload file will differ dependent on where your code is run. Also you will not need to reference the autoload file if you are using a framework like Zend Expressive.

**Install via Composer on the command line:**

```bash
composer require rbdwllr/psr-jwt
```

**Install via the composer.json file:**

```javascript
"require": {
    "rbdwllr/psr-jwt": "^0.2"
}
```

## Basic Usage

PsrJwt can be used with any PSR-7 / PSR-15 compliant framework. Just call one of the middleware factory methods and they will return a middleware instance that exposes two methods, `__invoke()` and `process()`. The later will work with PSR-15 compliant frameworks like Zend Expressive and the former will work with older PSR-7 compliant frameworks like Slim PHP v3.

```php
// Will generate a text/html response if JWT authentication fails.
\PsrJwt\Factory\JwtMiddleware::html('secret', 'tokenKey', 'body');

// Will generate an application/json response if JWT authentication fails.
\PsrJwt\Factory\JwtMiddleware::json('secret', 'tokenKey', ['body']);
```

The `secret` is the string required to hash the JSON Web Token signature.

The `tokenKey` is the key required to retrieve the JSON Web Token from a cookie, query parameter or the request body. By default though the library looks for tokens in the bearer field of the authorization header.

The `body` is the body content you would like to return in the response if authentication fails. For example, `<h1>Authentication Failed!</h1>`.

### Slim PHP Example Implementation

To implement the middleware in Slim PHP 3.0 you can use the code below.

```php
// Can be added to any routes file in Slim, often index.php.
require '../../vendor/autoload.php';

$app->get('/jwt', function (Request $request, Response $response) {
    $response->getBody()->write("JSON Web Token is Valid!");

    return $response;
})->add(\PsrJwt\Factory\JwtAuth::html('Secret123!456$', 'jwt', 'Authentication Failed'));
```

### Zend Expressive Example Implementation

```php
// Add to the config/pipeline.php file.
$app->pipe('/api', \PsrJwt\Factory\JwtAuth::html('!Secret#1XYZ$', 'jwt', 'Authentication Failed'));
```

### Generate JSON Web Token

To generate JSON Web Tokens PsrJwt offers a wrapper for the library [ReallySimpleJWT](https://github.com/RobDWaller/ReallySimpleJWT). You can create an instance of the ReallySimpleJWT builder by calling the built in factory method.

```php
require 'vendor/autoload.php';

\PsrJwt\Factory\Jwt::builder();
```

For more information on creating tokens please read the [ReallySimpleJWT](https://github.com/RobDWaller/ReallySimpleJWT/blob/master/readme.md) documentation.

## Advanced Usage

You don't have to use the factory methods explained above to generate the JWT authentication middleware you can call all the required classes directly. This allows you to configure a more customised setup and use your own handlers.

### Handlers

PsrJwt is built to work with any PSR 15 compliant handler. As standard it comes with two built in handlers, one which returns text/html responses and another which returns application/json responses.

You can use these handlers simply by instantiating them and passing them to the PsrJwt middleware.

```php
// Create Middleware with JSON handler.
use PsrJwt\Handler\Json;
use PsrJwt\JwtAuthMiddleware;


$auth = new Json($secret, $tokenKey, $body);

$middleware = new JwtAuthMiddleware($auth);
```

### Create Custom Handler

To create your own handler you need to do two things. First create a class which implements the `Psr\Http\Server\RequestHandlerInterface` [interface](https://www.php-fig.org/psr/psr-15/). This requires that you create a `handle()` method which consumes a `Psr\Http\Message\ServerRequestInterface` object and returns a `Psr\Http\Message\ResponseInterface` object.

Next you will need to extend the `PsrJwt\Auth\Authenticate` class as this will give you access to the JSON Web Token authentication functionality. Once this is done you will be able to pass your handler to the `PsrJwt\JwtAuthMiddleware` class and then integrate it with your desired framework.

```php
// An example JWT Authentication Handler.
use PsrJwt\Auth\Authenticate;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Response;

class MyHandler extends Authenticate implements RequestHandlerInterface
{
    public function __construct(string $secret, string $tokenKey)
    {
        parent::__construct($secret, $tokenKey);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $auth = $this->authenticate($request);

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
