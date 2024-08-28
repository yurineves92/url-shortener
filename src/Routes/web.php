<?php

namespace App\Routes;

use Slim\App;
use App\Controllers\UrlController;
use App\Controllers\ErrorController;

return function (App $app) {
    $app->get('/', [UrlController::class, 'home'])->setName('home');
    $app->post('/shorten', [UrlController::class, 'shorten'])->setName('shorten');
    $app->get('/{short_url_path:[a-zA-Z0-9]+}', [UrlController::class, 'redirect'])->setName('redirect');
    $app->get('/recent-urls', [UrlController::class, 'recentUrls'])->setName('recentUrls');

    $app->get('/not-found', [ErrorController::class, 'error'])->setName('error');

    $app->any('/{any:.*}', function ($request, $response) {
        return $response->withHeader('Location', '/not-found')->withStatus(302);
    })->setName('genericError');
};
