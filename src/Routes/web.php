<?php

namespace App\Routes;

use Slim\App;
use App\Controllers\UrlController;
use App\Controllers\ErrorController;
use App\Controllers\AuthController;
use App\Controllers\RolePermissionController;

return function (App $app) {
    // Auth routes
    $app->get('/login', [AuthController::class, 'showLoginForm'])->setName('login');
    $app->post('/login', [AuthController::class, 'login']);
    $app->get('/register', [AuthController::class, 'showRegisterForm'])->setName('register');
    $app->post('/register', [AuthController::class, 'register']);
    $app->get('/logout', [AuthController::class, 'logout'])->setName('logout');

    // Role and Permission routes
    $app->get('/roles', [RolePermissionController::class, 'listRoles'])->setName('listRoles');
    $app->get('/roles/create', [RolePermissionController::class, 'createRoleForm'])->setName('createRole');
    $app->post('/roles/create', [RolePermissionController::class, 'createRole']);
    $app->get('/permissions', [RolePermissionController::class, 'listPermissions'])->setName('listPermissions');
    $app->get('/permissions/create', [RolePermissionController::class, 'createPermissionForm'])->setName('createPermission');
    $app->post('/permissions/create', [RolePermissionController::class, 'createPermission']);
    $app->get('/roles-permissions/link', [RolePermissionController::class, 'linkRolePermissionForm'])->setName('linkRolePermission');
    $app->post('/roles-permissions/link', [RolePermissionController::class, 'linkRolePermission']);
    $app->get('/roles-permissions/unlink', [RolePermissionController::class, 'unlinkRolePermissionForm'])->setName('unlinkRolePermission');
    $app->post('/roles-permissions/unlink', [RolePermissionController::class, 'unlinkRolePermission']);
    $app->get('/roles-permissions', [RolePermissionController::class, 'listRolePermissions'])->setName('listRolePermissions');
    
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
