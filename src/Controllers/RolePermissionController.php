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

    public function linkRolePermissionForm(Request $request, Response $response): Response
    {
        try {
            $roles = $this->rolePermissionModel->getAllRoles();
            $permissions = $this->rolePermissionModel->getAllPermissions();

            return $this->view->render($response, 'role_permission/link_role_permission.twig', [
                'roles' => $roles,
                'permissions' => $permissions
            ]);
        } catch (Exception $e) {
            error_log('Error displaying link role-permission form: ' . $e->getMessage());
            return $this->view->render($response, 'error.twig', [
                'message' => 'Error displaying link role-permission form.'
            ]);
        }
    }

    public function linkRolePermission(Request $request, Response $response): Response
    {
        $params = (array)$request->getParsedBody();
        $roleId = $params['role_id'] ?? '';
        $permissionId = $params['permission_id'] ?? '';

        if (empty($roleId) || empty($permissionId)) {
            return $this->view->render($response, 'role_permission/link_role_permission.twig', [
                'message' => 'Role ID and Permission ID cannot be empty.',
                'alertType' => 'danger'
            ]);
        }

        try {
            $this->rolePermissionModel->linkRolePermission($roleId, $permissionId);

            return $response->withHeader('Location', '/roles-permissions')->withStatus(302);
        } catch (Exception $e) {
            return $this->view->render($response, 'role_permission/link_role_permission.twig', [
                'message' => 'Error linking role to permission: ' . $e->getMessage(),
                'alertType' => 'danger'
            ]);
        }
    }

    public function unlinkRolePermissionForm(Request $request, Response $response): Response
    {
        try {
            $roles = $this->rolePermissionModel->getAllRoles();
            $permissions = $this->rolePermissionModel->getAllPermissions();

            return $this->view->render($response, 'role_permission/unlink_role_permission.twig', [
                'roles' => $roles,
                'permissions' => $permissions
            ]);
        } catch (Exception $e) {
            error_log('Error displaying unlink role-permission form: ' . $e->getMessage());
            return $this->view->render($response, 'error.twig', [
                'message' => 'Error displaying unlink role-permission form.'
            ]);
        }
    }

    public function unlinkRolePermission(Request $request, Response $response): Response
    {
        $params = (array)$request->getParsedBody();
        $roleId = $params['role_id'] ?? '';
        $permissionId = $params['permission_id'] ?? '';

        if (empty($roleId) || empty($permissionId)) {
            return $this->view->render($response, 'role_permission/unlink_role_permission.twig', [
                'message' => 'Role ID and Permission ID cannot be empty.',
                'alertType' => 'danger'
            ]);
        }

        try {
            $this->rolePermissionModel->unlinkRolePermission($roleId, $permissionId);

            return $response->withHeader('Location', '/roles-permissions')->withStatus(302);
        } catch (Exception $e) {
            return $this->view->render($response, 'role_permission/unlink_role_permission.twig', [
                'message' => 'Error unlinking role from permission: ' . $e->getMessage(),
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
