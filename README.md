# PSR-JWT
[![Build Status](https://travis-ci.org/RobDWaller/psr-jwt.svg?branch=master)](https://travis-ci.org/RobDWaller/psr-jwt) [![codecov](https://codecov.io/gh/RobDWaller/psr-jwt/branch/master/graph/badge.svg)](https://codecov.io/gh/RobDWaller/psr-jwt) [![Infection MSI](https://badge.stryker-mutator.io/github.com/RobDWaller/psr-jwt/master)](https://infection.github.io)

A PSR-7 and PSR-15 compliant JSON Web Token Middleware Library. Currently in alpha and built on top of [ReallySimpleJWT](https://github.com/RobDWaller/ReallySimpleJWT).

The library allows you to create JSON Web Tokens and then validate them using PSR-15 compliant middleware which can be added to compatible frameworks.

For more information on JSON Web Tokens please read [RFC 7519](https://tools.ietf.org/html/rfc7519). Also to learn more about how to pass JSON Web Tokens to web applications please read up on bearer token authorization in [RFC 6750](https://tools.ietf.org/html/rfc6750).

## Setup

Via Composer on the command line:

```bash
composer require rbdwllr/psr-jwt
```

Via composer.json:

```javascript
"require": {
    "rbdwllr/psr-jwt": "^0.1"
}
```

## Usage

PSR-JWT can be used with any PSR-7 / PSR-15 compliant framework. Just call the middleware factory method and it will return a middleware instance that exposes two methods, `__invoke()` and `process()`. The later will work with PSR-15 compliant frameworks like Zend Expressive and the former will work with older PSR-7 compliant frameworks like Slim PHP v3.

```php
\PsrJwt\Factory\JwtAuth::middleware('tokenKey', 'secret');
```

The `tokenKey` is the key required to retrieve the JSON Web Token from a cookie, query parameter or the request body. By default though the library looks for tokens in bearer field of the authorization header.

The `secret` is the string required to hash the JSON Web Token signature.

### Slim PHP 3.0 Example Implementation

```php
// Can be added to any routes file in Slim, often index.php.
$app->get('/jwt', function (Request $request, Response $response) {
    $response->getBody()->write("JSON Web Token is Valid!");

    return $response;
})->add(\PsrJwt\Factory\JwtAuth::middleware('jwt', 'Secret123!456$'));
```

### Zend Expressive Example Implementation

```php
// Add to the config/pipeline.php file.
$app->pipe('/api', \PsrJwt\Factory\JwtAuth::middleware('jwt', '!secReT$123*'));
```

### Generate JSON Web Token

To generate JSON Web Tokens PsrJwt offers a wrapper for the library ReallySimpleJWT. You can create an instance of the ReallySimpleJWT builder by calling the built in factory method.

```php
\PsrJwt\Factory\Jwt::builder();
```

For more information on creating tokens please read the ReallySimpleJWT documentation.
