<?php

use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

// Crie a aplicação Slim
$app = AppFactory::create();

// Crie o Twig Middleware
$twig = Twig::create(__DIR__ . '/../src/Views', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));

// Inclua as rotas
(require __DIR__ . '/../src/Routes/web.php')($app);

// Execute a aplicação
$app->run();
