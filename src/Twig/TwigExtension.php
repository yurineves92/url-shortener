<?php

namespace App\Twig;

use Slim\Interfaces\RouteParserInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    private $router;

    public function __construct(RouteParserInterface $router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('path_for', [$this, 'pathFor']),
        ];
    }

    public function pathFor($name, $data = [], $queryParams = [])
    {
        return $this->router->urlFor($name, $data, $queryParams);
    }
}
