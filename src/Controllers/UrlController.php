<?php

namespace App\Controllers;

use App\Models\Url;
use App\Models\UrlLog;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;
use Exception;

class UrlController
{
    protected $view;
    private $urlModel;
    private $urlLogModel;

    public function __construct(Twig $view, PDO $pdo)
    {
        $this->view = $view;
        $this->urlModel = new Url($pdo);
        $this->urlLogModel = new UrlLog($pdo);
    }

    public function home(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'home.twig');
    }

    public function shorten(Request $request, Response $response)
    {
        $params = (array) $request->getParsedBody();
        $longUrl = $params['long_url'] ?? '';
        $shortUrlPath = substr(md5(uniqid((string) mt_rand(), true)), 0, 7);

        try {
            $uuid = bin2hex(random_bytes(16));
            $this->urlModel->createUrl($longUrl, $shortUrlPath, 'RANDOM', 0.00, null, $uuid);

            $uri = $request->getUri();
            $baseUrl = $uri->getScheme() . '://' . $uri->getHost() . ($uri->getPort() ? ':' . $uri->getPort() : '');

            $message = 'URL encurtada com sucesso!';
            $alertType = 'success';

            return $this->view->render($response, 'result.twig', [
                'short_url' => $baseUrl . '/' . $shortUrlPath,
                'message' => $message,
                'alertType' => $alertType
            ]);
        } catch (Exception $e) {
            $message = 'Erro ao encurtar a URL: ' . $e->getMessage();
            $alertType = 'danger';

            return $this->view->render($response, 'home.twig', [
                'message' => $message,
                'alertType' => $alertType
            ]);
        }
    }

    public function redirect(Request $request, Response $response, array $args)
    {
        $shortUrlPath = $args['short_url_path'];
        $url = $this->urlModel->getUrlByShortPath($shortUrlPath);

        if (is_array($url)) {
            $uuid = bin2hex(random_bytes(16));
            $this->urlLogModel->logAccess($uuid, $url['id']);

            return $response->withHeader('Location', $url['long_url'])->withStatus(302);
        } else {
            $response->getBody()->write('URL não encontrada.');
            return $response->withStatus(404);
        }
    }

    public function recentUrls(Request $request, Response $response): Response
    {
        $recentUrls = $this->urlModel->getRecentUrls();
        return $this->view->render($response, 'recent_urls.twig', [
            'recent_urls' => $recentUrls
        ]);
    }
}
