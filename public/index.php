<?php

use App\Controllers\AuthController;
use App\Controllers\ErrorController;

use Slim\Factory\AppFactory;

use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

use DI\Container;

use App\Controllers\UrlController;

use App\Twig\TwigExtension;

use Slim\Interfaces\RouteParserInterface;

use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$container = new Container();
$app = AppFactory::createFromContainer($container);

session_start();

$container->set('view', function ($container) use ($app) {
    $twig = Twig::create(__DIR__ . '/../src/Views', ['cache' => false]);
    $router = $container->get(RouteParserInterface::class);
    $twig->addExtension(new TwigExtension($router));

    require __DIR__ . '/../src/Config/TwigConfig.php';
    configureTwig($twig->getEnvironment());

    return $twig;
});

$container->set('pdo', function () {
    $dsn = $_ENV['DB_DSN'];
    $username = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASS'];
    return new PDO($dsn, $username, $password);
});

$container->set(RouteParserInterface::class, function ($container) use ($app) {
    return $app->getRouteCollector()->getRouteParser();
});

$container->set(AuthController::class, function (Container $container) {
    return new AuthController(
        $container->get('view'),
        $container->get('pdo')
    );
});

$container->set(UrlController::class, function (Container $container) {
    return new UrlController(
        $container->get('view'),
        $container->get('pdo')
    );
});

$container->set(ErrorController::class, function (Container $container) {
    return new ErrorController(
        $container->get('view')
    );
});

$app->add(TwigMiddleware::create($app, $container->get('view')));

(require __DIR__ . '/../src/Routes/web.php')($app);

$app->run();
