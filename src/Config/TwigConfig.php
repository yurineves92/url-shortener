<?php

use Twig\Environment;
use Slim\Views\Twig;

function configureTwig(Environment $twig)
{
    $twig->addGlobal('is_logged_in', isset($_SESSION['user_id']));
    $twig->addGlobal('user_role_id', isset($_SESSION['user_role_id']));

    return $twig;
}
