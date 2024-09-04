<?php

namespace App\Controllers;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;
use Exception;

class RolePermissionController
{
    protected $view;
    private $pdo;

    public function __construct(Twig $view, PDO $pdo)
    {
        $this->view = $view;
        $this->pdo = $pdo;
    }

    public function listRoles(Request $request, Response $response): Response
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM roles");
            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            $stmt = $this->pdo->prepare("INSERT INTO roles (name) VALUES (:name)");
            $stmt->execute(['name' => $name]);

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
            $stmt = $this->pdo->query("SELECT * FROM permissions");
            $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            $stmt = $this->pdo->prepare("INSERT INTO permissions (name) VALUES (:name)");
            $stmt->execute(['name' => $name]);

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
            $rolesStmt = $this->pdo->query("SELECT * FROM roles");
            $roles = $rolesStmt->fetchAll(PDO::FETCH_ASSOC);

            $permissionsStmt = $this->pdo->query("SELECT * FROM permissions");
            $permissions = $permissionsStmt->fetchAll(PDO::FETCH_ASSOC);

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
            $stmt = $this->pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)");
            $stmt->execute([
                'role_id' => $roleId,
                'permission_id' => $permissionId
            ]);

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
            $rolesStmt = $this->pdo->query("SELECT * FROM roles");
            $roles = $rolesStmt->fetchAll(PDO::FETCH_ASSOC);

            $permissionsStmt = $this->pdo->query("SELECT * FROM permissions");
            $permissions = $permissionsStmt->fetchAll(PDO::FETCH_ASSOC);

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
            $stmt = $this->pdo->prepare("DELETE FROM role_permissions WHERE role_id = :role_id AND permission_id = :permission_id");
            $stmt->execute([
                'role_id' => $roleId,
                'permission_id' => $permissionId
            ]);

            return $response->withHeader('Location', '/roles-permissions')->withStatus(302);
        } catch (Exception $e) {
            return $this->view->render($response, 'role_permission/unlink_role_permission.twig', [
                'message' => 'Error unlinking role from permission: ' . $e->getMessage(),
                'alertType' => 'danger'
            ]);
        }
    }
}
