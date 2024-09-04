<?php

namespace App\Controllers;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use PDO;
use Exception;

class AuthController
{
    protected $view;
    private $pdo;

    public function __construct(Twig $view, PDO $pdo)
    {
        $this->view = $view;
        $this->pdo = $pdo;
    }

    public function showLoginForm(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'auth/login.twig', [
            'current_route' => 'login'
        ]);
    }

    public function login(Request $request, Response $response): Response
    {
        $params = $request->getParsedBody();
        $email = filter_var($params['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $params['password'] ?? '';
    
        if (!$email || empty($password)) {
            $message = 'E-mail ou senha inválidos.';
            $alertType = 'danger';
    
            return $this->view->render($response, 'auth/login.twig', [
                'message' => $message,
                'alertType' => $alertType,
                'current_route' => 'login'
            ]);
        }
    
        try {
            $stmt = $this->pdo->prepare("SELECT id, email, password FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
    
                return $response->withHeader('Location', '/')->withStatus(302);
            } else {
                $message = 'E-mail ou senha incorretos.';
                $alertType = 'danger';
    
                return $this->view->render($response, 'auth/login.twig', [
                    'message' => $message,
                    'alertType' => $alertType,
                    'current_route' => 'login'
                ]);
            }
        } catch (Exception $e) {
            error_log('Login error: ' . $e->getMessage());
    
            $message = 'Ocorreu um erro ao processar o login. Tente novamente mais tarde.';
            $alertType = 'danger';
    
            return $this->view->render($response, 'auth/login.twig', [
                'message' => $message,
                'alertType' => $alertType,
                'current_route' => 'login'
            ]);
        }
    }
    
    
    public function logout(Request $request, Response $response): Response
    {
        session_destroy();

        return $response->withHeader('Location', '/login')->withStatus(302);
    }

    public function showRegisterForm(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'auth/register.twig');
    }

    public function register(Request $request, Response $response): Response
    {
        $params = (array)$request->getParsedBody();
        $email = filter_var($params['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $params['password'] ?? '';
        $confirmPassword = $params['confirm_password'] ?? '';

        if (!$email || empty($password) || $password !== $confirmPassword) {
            return $this->view->render($response, 'auth/register.twig', [
                'message' => 'As senhas não coincidem ou dados inválidos.',
                'alertType' => 'danger'
            ]);
        }

        try {
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                return $this->view->render($response, 'auth/register.twig', [
                    'message' => 'E-mail já está em uso.',
                    'alertType' => 'danger'
                ]);
            }

            $stmt = $this->pdo->prepare("INSERT INTO users (email, password, active, role_id) VALUES (:email, :password, :active, :role_id)");
            $stmt->execute([
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'active' => 1,
                'role_id' => 2
            ]);

            return $this->view->render($response, 'auth/login.twig', [
                'message' => 'Registro bem-sucedido! Agora você pode fazer login.',
                'alertType' => 'success'
            ]);
        } catch (Exception $e) {
            return $this->view->render($response, 'auth/register.twig', [
                'message' => 'Erro ao registrar: ' . $e->getMessage(),
                'alertType' => 'danger'
            ]);
        }
    }
}
