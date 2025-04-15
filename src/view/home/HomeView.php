<?php

namespace Vosouza\Sonomais\view\home;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class HomeView
{
    private Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader('templates'); // Caminho para seus templates
        $this->twig = new Environment($loader);
    }

    // public function exibirDetalhes(array $produto): void
    // {
    //     echo $this->twig->render('home.html', ['produto' => $produto]);
    // }

    public function show(array $product): void
    {
        echo $this->twig->render('home.html', ['produtos' => $product]);
    }

    // Outros métodos de exibição...
}