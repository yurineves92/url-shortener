<?php

use Twig\Environment;
use Slim\Views\Twig;

function configureTwig(Environment $twig)
{
    $twig->addGlobal('is_logged_in', isset($_SESSION['user_id']));

    return $twig;
}
