<?php

use Twig\Environment;
function configureTwig(Environment $twig)
{
    $twig->addGlobal('is_logged_in', isset($_SESSION['user_id']));
    $twig->addGlobal('user_role_id', $_SESSION['user_role_id'] ?? null);

    return $twig;
}
