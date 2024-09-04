<?php

namespace App\Routes;

use Slim\App;
use App\Middleware\PermissionMiddleware;
use App\Controllers\UrlController;
use App\Controllers\ErrorController;
use App\Controllers\AuthController;
use App\Controllers\RolePermissionController;

return function (App $app) {
    $container = $app->getContainer();

    $adminMiddleware = new PermissionMiddleware(['administrador'], $container);
    $userMiddleware = new PermissionMiddleware(['usuário', 'administrador'], $container);
    $guestMiddleware = new PermissionMiddleware(['convidado', 'usuário', 'administrador'], $container);

    $app->get('/login', [AuthController::class, 'showLoginForm'])->setName('login');
    $app->post('/login', [AuthController::class, 'login']);
    $app->get('/register', [AuthController::class, 'showRegisterForm'])->setName('register');
    $app->post('/register', [AuthController::class, 'register']);
    $app->get('/logout', [AuthController::class, 'logout'])->setName('logout');

    $app->get('/roles', [RolePermissionController::class, 'listRoles'])->setName('listRoles')->add($adminMiddleware);
    $app->get('/roles/form', [RolePermissionController::class, 'createRoleForm'])->setName('createRoleForm')->add($adminMiddleware);
    $app->post('/roles/create', [RolePermissionController::class, 'createRole'])->setName('createRole')->add($adminMiddleware);
    $app->get('/permissions', [RolePermissionController::class, 'listPermissions'])->setName('listPermissions')->add($adminMiddleware);
    $app->get('/permissions/form', [RolePermissionController::class, 'createPermissionForm'])->setName('createPermissionForm')->add($adminMiddleware);
    $app->post('/permissions/create', [RolePermissionController::class, 'createPermission'])->setName('createPermission')->add($adminMiddleware);
    $app->get('/roles-permissions', [RolePermissionController::class, 'listRolePermissions'])->setName('listRolePermissions')->add($adminMiddleware);
    $app->get('/roles-permissions/{type}', [RolePermissionController::class, 'showRolePermissionForm'])->setName('rolePermissionForm')->add($adminMiddleware);
    $app->post('/roles-permissions/{type}', [RolePermissionController::class, 'processRolePermission'])->setName('processRolePermission')->add($adminMiddleware);

    $app->get('/not-found', [ErrorController::class, 'error'])->setName('error');
    $app->get('/unauthorized', [ErrorController::class, 'unauthorized'])->setName('unauthorized');

    $app->get('/', [UrlController::class, 'home'])->setName('home')->add($guestMiddleware);
    $app->post('/shorten', [UrlController::class, 'shorten'])->setName('shorten')->add($userMiddleware);
    $app->get('/recent-urls', [UrlController::class, 'recentUrls'])->setName('recentUrls')->add($userMiddleware);
    $app->get('/{short_url_path:[a-zA-Z0-9]+}', [UrlController::class, 'redirect'])->setName('redirect')->add($userMiddleware);
    $app->get('/qrcode/{short_url_path:[a-zA-Z0-9]+}', [UrlController::class, 'generateQrCode'])->setName('generateQrCode')->add($userMiddleware);


    $app->any('/{any:.*}', function ($request, $response) {
        return $response->withHeader('Location', '/not-found')->withStatus(302);
    })->setName('genericError');
};
