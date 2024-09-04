<?php

namespace App\Controllers;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ErrorController
{
    private $view;
    
    public function __construct(Twig $view)
    {
        $this->view = $view;
    }

    public function error(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'error.twig');
    }

    public function unauthorized(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'unauthorized.twig');
    }
}