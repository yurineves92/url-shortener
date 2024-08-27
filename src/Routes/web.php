<?php

namespace App\Routes;

use Slim\App;
use App\Controllers\UrlController;

return function (App $app) {
    $app->get('/', [UrlController::class, 'home'])->setName('home');
    $app->post('/shorten', [UrlController::class, 'shorten'])->setName('shorten');
    $app->get('/{short_url_path:[a-zA-Z0-9]+}', [UrlController::class, 'redirect'])->setName('redirect');
};
