# Demo application

### Libraries

The following libraries are used:

- relay/relay: PSR-15 server request handler
- middlewares/fast-route: Middleware to use FastRoute for handler discovery.
- middlewares/request-handler: Middleware to execute request handlers discovered by a router
- narrowspark/http-emitter: returning HTTP responses
- php-di/php-di: IOC container
- laminas/laminas-diactoros:  PSR-7 HTTP message interfaces & PSR-17 HTTP message factory interfaces
- vlucas/phpdotenv: handling .env
- phpunit/phpunit: tests

## Requirements

- PHP 8.0+

## Install

```
composer install
```

### Configure

Create and configure `.env` file

```
cp .env.example .env
vi .env
```

### Run

```
php -S localhost:8080 -t public/
```

## Test

Run phpunit tests with

```
composer test
```

## TODO
- add logging
- integrate Doctrine => ORM
- integrate Swagger => Documentation, OpenAPI