<?php

namespace Vosouza\Sonomais\view\login;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Vosouza\Sonomais\view\ViewInterface;

class LoginView implements ViewInterface
{
    private Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__); // Caminho para seus templates
        $this->twig = new Environment($loader);
    }

    public function show(array $data = []): void
    {
        $product = [];
        echo $this->twig->render('login.html.twig', $data);
    }

}