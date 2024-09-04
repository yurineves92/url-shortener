<?php

namespace App\Controllers;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\RolePermission;
use Exception;
use PDO;
class RolePermissionController
{
    protected $view;
    private $rolePermissionModel;

    public function __construct(Twig $view, PDO $pdo)
    {
        $this->view = $view;
        $this->rolePermissionModel = new RolePermission($pdo);
    }

    public function listRoles(Request $request, Response $response): Response
    {
        try {
            $roles = $this->rolePermissionModel->getAllRoles();

            return $this->view->render($response, 'role_permission/roles.twig', [
                'roles' => $roles
            ]);
        } catch (Exception $e) {
            error_log('Error listing roles: ' . $e->getMessage());
            return $this->view->render($response, 'error.twig', [
                'message' => 'Error listing roles.'
            ]);
        }
    }

    public function createRoleForm(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'role_permission/create_role.twig');
    }

    public function createRole(Request $request, Response $response): Response
    {
        $params = (array)$request->getParsedBody();
        $name = $params['name'] ?? '';

        if (empty($name)) {
            return $this->view->render($response, 'role_permission/create_role.twig', [
                'message' => 'Role name cannot be empty.',
                'alertType' => 'danger'
            ]);
        }

        try {
            $this->rolePermissionModel->createRole($name);

            return $response->withHeader('Location', '/roles')->withStatus(302);
        } catch (Exception $e) {
            return $this->view->render($response, 'role_permission/create_role.twig', [
                'message' => 'Error creating role: ' . $e->getMessage(),
                'alertType' => 'danger'
            ]);
        }
    }

    public function listPermissions(Request $request, Response $response): Response
    {
        try {
            $permissions = $this->rolePermissionModel->getAllPermissions();

            return $this->view->render($response, 'role_permission/permissions.twig', [
                'permissions' => $permissions
            ]);
        } catch (Exception $e) {
            error_log('Error listing permissions: ' . $e->getMessage());
            return $this->view->render($response, 'error.twig', [
                'message' => 'Error listing permissions.'
            ]);
        }
    }

    public function createPermissionForm(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'role_permission/create_permission.twig');
    }

    public function createPermission(Request $request, Response $response): Response
    {
        $params = (array)$request->getParsedBody();
        $name = $params['name'] ?? '';

        if (empty($name)) {
            return $this->view->render($response, 'role_permission/create_permission.twig', [
                'message' => 'Permission name cannot be empty.',
                'alertType' => 'danger'
            ]);
        }

        try {
            $this->rolePermissionModel->createPermission($name);

            return $response->withHeader('Location', '/permissions')->withStatus(302);
        } catch (Exception $e) {
            return $this->view->render($response, 'role_permission/create_permission.twig', [
                'message' => 'Error creating permission: ' . $e->getMessage(),
                'alertType' => 'danger'
            ]);
        }
    }

    public function showRolePermissionForm(Request $request, Response $response, $type): Response
    {
        if (is_array($type)) {
            $type = reset($type);
        }
    
        if (!is_string($type) || !in_array($type, ['link', 'unlink'])) {
            return $this->view->render($response, 'error.twig', [
                'message' => 'Tipo de ação inválido.'
            ]);
        }
    
        try {
            $roles = $this->rolePermissionModel->getAllRoles();
            $permissions = $this->rolePermissionModel->getAllPermissions();
    
            return $this->view->render($response, "role_permission/{$type}_role_permission.twig", [
                'roles' => $roles,
                'permissions' => $permissions
            ]);
        } catch (Exception $e) {

            error_log('Error displaying role-permission form: ' . $e->getMessage());
            return $this->view->render($response, 'error.twig', [
                'message' => 'Erro ao exibir o formulário de perfil e permissão.'
            ]);
        }
    }
    
    public function processRolePermission(Request $request, Response $response, $type): Response
    {
        if (is_array($type)) {
            $type = reset($type);
        }
    
        if (!is_string($type) || !in_array($type, ['link', 'unlink'])) {
            return $this->view->render($response, 'error.twig', [
                'message' => 'Tipo de ação inválido.'
            ]);
        }
        
        if (!in_array($type, ['link', 'unlink'])) {
            return $this->view->render($response, "role_permission/{$type}_role_permission.twig", [
                'message' => 'Tipo de ação inválido.',
                'alertType' => 'danger'
            ]);
        }
    
        $params = (array)$request->getParsedBody();
        $roleId = $params['role_id'] ?? '';
        $permissionId = $params['permission_id'] ?? '';
    
        if (empty($roleId) || empty($permissionId)) {
            return $this->view->render($response, "role_permission/{$type}_role_permission.twig", [
                'message' => 'Role ID e Permission ID não podem estar vazios.',
                'alertType' => 'danger'
            ]);
        }
    
        try {
            if ($type === 'link') {
                $this->rolePermissionModel->linkRolePermission($roleId, $permissionId);
                $message = 'Permissão vinculada com sucesso.';
            } elseif ($type === 'unlink') {
                $this->rolePermissionModel->unlinkRolePermission($roleId, $permissionId);
                $message = 'Permissão desvinculada com sucesso.';
            }
    
            return $response->withHeader('Location', '/roles-permissions')->withStatus(302);
        } catch (Exception $e) {
            return $this->view->render($response, "role_permission/{$type}_role_permission.twig", [
                'message' => 'Erro ao ' . ($type === 'link' ? 'vincular' : 'desvincular') . ' permissão ao perfil: ' . $e->getMessage(),
                'alertType' => 'danger'
            ]);
        }
    }

    public function listRolePermissions(Request $request, Response $response): Response
    {
        $roles = [];
        try {
            $rolesData = $this->rolePermissionModel->getAllRoles();
            foreach ($rolesData as $role) {
                $permissions = $this->rolePermissionModel->getPermissionsForRole($role['id']);
                $roles[] = [
                    'role' => $role,
                    'permissions' => $permissions
                ];
            }

            return $this->view->render($response, 'role_permission/roles_permissions.twig', [
                'roles' => $roles
            ]);
        } catch (Exception $e) {
            error_log('Error listing roles and permissions: ' . $e->getMessage());
            return $this->view->render($response, 'error.twig', [
                'message' => 'Error listing roles and permissions.'
            ]);
        }
    }
}
