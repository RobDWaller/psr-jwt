# PSR-JWT
[![Build Status](https://travis-ci.org/RobDWaller/psr-jwt.svg?branch=master)](https://travis-ci.org/RobDWaller/psr-jwt) [![codecov](https://codecov.io/gh/RobDWaller/psr-jwt/branch/master/graph/badge.svg)](https://codecov.io/gh/RobDWaller/psr-jwt) [![Infection MSI](https://badge.stryker-mutator.io/github.com/RobDWaller/psr-jwt/master)](https://infection.github.io) [![StyleCI](https://github.styleci.io/repos/167511682/shield?branch=master)](https://github.styleci.io/repos/167511682) [![Latest Stable Version](https://poser.pugx.org/rbdwllr/psr-jwt/v/stable)](https://packagist.org/packages/rbdwllr/psr-jwt) ![PHP Version Support](https://img.shields.io/travis/php-v/RobDWaller/psr-jwt/master)

A [PSR-7](https://www.php-fig.org/psr/psr-7/) and [PSR-15](https://www.php-fig.org/psr/psr-15/) compliant JSON Web Token middleware library built on top of [ReallySimpleJWT](https://github.com/RobDWaller/ReallySimpleJWT).

The library allows you to create JSON Web Tokens and then validate them using PSR-15 compliant middleware which can be added to compatible frameworks such as [Slim PHP](http://www.slimframework.com/) and [Zend Expressive](https://docs.zendframework.com/zend-expressive/).

For more information on JSON Web Tokens please read [RFC 7519](https://tools.ietf.org/html/rfc7519). Also to learn more about how to pass JSON Web Tokens to web applications please read up on bearer token authorization in [RFC 6750](https://tools.ietf.org/html/rfc6750).

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
    "rbdwllr/psr-jwt": "^0.1"
}
```

## Basic Usage

PSR-JWT can be used with any PSR-7 / PSR-15 compliant framework. Just call the middleware factory method and it will return a middleware instance that exposes two methods, `__invoke()` and `process()`. The later will work with PSR-15 compliant frameworks like Zend Expressive and the former will work with older PSR-7 compliant frameworks like Slim PHP v3.

```php
\PsrJwt\Factory\JwtAuth::middleware('secret', 'tokenKey', 'body');
```

The `secret` is the string required to hash the JSON Web Token signature.

The `tokenKey` is the key required to retrieve the JSON Web Token from a cookie, query parameter or the request body. By default though the library looks for tokens in the bearer field of the authorization header.

The `body` is the body content you would like to return in the response if authentication fails.

### Slim PHP Example Implementation

To implement the middleware in Slim PHP 3.0 you can use the code below.

```php
// Can be added to any routes file in Slim, often index.php.
require '../../vendor/autoload.php';

$app->get('/jwt', function (Request $request, Response $response) {
    $response->getBody()->write("JSON Web Token is Valid!");

    return $response;
})->add(\PsrJwt\Factory\JwtAuth::middleware('Secret123!456$', 'jwt', 'Authentication Failed'));
```

### Zend Expressive Example Implementation

```php
// Add to the config/pipeline.php file.
$app->pipe('/api', \PsrJwt\Factory\JwtAuth::middleware('!Secret#1XYZ$', 'jwt', 'Authentication Failed'));
```

### JSON Response Handler

The default Auth handler returns a Text / HTML response, there is though a JSON handler too. This can be implemented by calling the `jsonMiddleware()` method in the JwtAuth factory.

```php
PsrJwt\Factory\JwtAuth::jsonMiddleware('Secret123!456$', 'jwt', ['Response Body']));
```

### Generate JSON Web Token

To generate JSON Web Tokens PsrJwt offers a wrapper for the library [ReallySimpleJWT](https://github.com/RobDWaller/ReallySimpleJWT). You can create an instance of the ReallySimpleJWT builder by calling the built in factory method.

```php
require 'vendor/autoload.php';

\PsrJwt\Factory\Jwt::builder();
```

For more information on creating tokens please read the [ReallySimpleJWT](https://github.com/RobDWaller/ReallySimpleJWT/blob/master/readme.md) documentation.

## Advanced Usage

### Handlers

PSR-JWT is built to work with any PSR 15 compliant handler. As standard it comes with two built in handlers, one which returns Text / HTML and another which returns JSON.

You can use these handlers simply by instantiating them and passing them to the PSR-JWT middleware.

```php
//Example JSON handler.
$auth = new JsonAuth($secret, $tokenKey, $body);

return new JwtAuthMiddleware($auth);
```

### Create Custom Handler

You can create your own custom handler by simply implementing the `Psr\Http\Server\RequestHandlerInterface` interface (Read [PHP-FIG](https://www.php-fig.org/psr/psr-15/) for more details). This requires that you implement a `handle()` method which consumes a `Psr\Http\Message\ServerRequestInterface` object and returns a `Psr\Http\Message\ResponseInterface` object.

To gain the JWT authentication / validation functionality you simply need to extend the PSR-JWT `PsrJwt\Auth\Authenticate` class.

```php
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
