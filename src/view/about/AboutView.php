<?php

namespace Vosouza\Sonomais\view\about;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Vosouza\Sonomais\view\ViewInterface;

class AboutView implements ViewInterface
{
    private Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader([__DIR__, __DIR__.'/../templates'] ); // Caminho para seus templates
        $this->twig = new Environment($loader);
    }

    public function show(array $data = []): void
    {
        echo $this->twig->render('about.html.twig', $data);
    }

}