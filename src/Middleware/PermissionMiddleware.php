<?php

namespace App\Middleware;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Models\RoleModel;
use DI\Container;

class PermissionMiddleware
{
    protected $requiredPermissions;
    protected $container;

    public function __construct(array $requiredPermissions, Container $container)
    {
        $this->requiredPermissions = $requiredPermissions;
        $this->container = $container;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $user = $_SESSION;

        $routePath = $request->getUri()->getPath();
        
        if (!isset($user['user_role_id'])) {
            return $this->redirect('/unauthorized');
        }

        $pdo = $this->container->get('pdo');
        $roleModel = new RoleModel($pdo);
        $permissions = $roleModel->getPermissionsByRoleId($user['user_role_id']);
        
        if (!in_array($routePath, $permissions)) {
            return $this->redirect('/unauthorized');
        }

        return $handler->handle($request);
    }

    private function redirect(string $url): Response
    {
        $response = new \Slim\Psr7\Response();
        return $response->withHeader('Location', $url)->withStatus(302);
    }
}
