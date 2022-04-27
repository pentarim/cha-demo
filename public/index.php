<?php declare(strict_types=1);

use DI\ContainerBuilder;
use FastRoute\RouteCollector;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Narrowspark\HttpEmitter\SapiEmitter;
use Relay\Relay;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use function DI\create;
use function DI\get;
use function FastRoute\simpleDispatcher;
use App\Controllers\UserController;
use App\Managers\UserManager;
use App\Db;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(false);
$containerBuilder->useAnnotations(false);
$containerBuilder->addDefinitions([
    UserController::class => create(UserController::class)
        ->constructor(get('Response'), get(UserManager::class)),
    UserManager::class => create(UserManager::class)
        ->constructor(get(Db::class)),
    Db::class => function() {
        return new Db(
            $_ENV['DB_HOST'] ?? 'example.com',
            $_ENV['DB_USER'] ?? 'user',
            $_ENV['DB_PASS'] ?? 'password',
            $_ENV['DB_NAME'] ?? 'database',
            intval($_ENV['DB_PORT'] ?? '3306'),
        );
    },
    'Response' => function() {
        return new Response();
    },
]);

$container = $containerBuilder->build();

$routes = simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('GET', '/user/{id:[0-9]+}', [UserController::class, 'showAction']);
    $r->addRoute('POST', '/user', [UserController::class, 'createAction']);
    $r->addRoute(['POST','PATCH'], '/user/{id:[0-9]+}', [UserController::class, 'updateAction']);
});

$middlewareQueue[] = new FastRoute($routes);
$middlewareQueue[] = new RequestHandler($container);

/** @noinspection PhpUnhandledExceptionInspection */
$requestHandler = new Relay($middlewareQueue);
$response = $requestHandler->handle(ServerRequestFactory::fromGlobals());

$emitter = new SapiEmitter();
/** @noinspection PhpVoidFunctionResultUsedInspection */
return $emitter->emit($response);