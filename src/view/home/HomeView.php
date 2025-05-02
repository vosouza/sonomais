<?php

namespace Vosouza\Sonomais\view\home;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Vosouza\Sonomais\view\ViewInterface;

class HomeView implements ViewInterface
{
    private Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ ); // Caminho para seus templates
        $this->twig = new Environment($loader);
    }

    public function show(array $data = []): void
    {
        echo $this->twig->render('home.html.twig', ['products' => $data]);
    }

}