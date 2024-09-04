<?php

namespace App\Routes;

use Slim\App;
use App\Controllers\UrlController;
use App\Controllers\ErrorController;
use App\Controllers\AuthController;

return function (App $app) {
    // Auth routes
    $app->get('/login', [AuthController::class, 'showLoginForm'])->setName('login');
    $app->post('/login', [AuthController::class, 'login']);
    $app->get('/register', [AuthController::class, 'showRegisterForm'])->setName('register');
    $app->post('/register', [AuthController::class, 'register']);
    $app->get('/logout', [AuthController::class, 'logout'])->setName('logout');

    // Url routes
    $app->get('/', [UrlController::class, 'home'])->setName('home');
    $app->post('/shorten', [UrlController::class, 'shorten'])->setName('shorten');
    $app->get('/{short_url_path:[a-zA-Z0-9]+}', [UrlController::class, 'redirect'])->setName('redirect');
    $app->get('/recent-urls', [UrlController::class, 'recentUrls'])->setName('recentUrls');
    $app->get('/qrcode/{short_url_path:[a-zA-Z0-9]+}', [UrlController::class, 'generateQrCode'])->setName('generateQrCode');

    // Routes not found
    $app->get('/not-found', [ErrorController::class, 'error'])->setName('error');

    // Generic
    $app->any('/{any:.*}', function ($request, $response) {
        return $response->withHeader('Location', '/not-found')->withStatus(302);
    })->setName('genericError');
};
