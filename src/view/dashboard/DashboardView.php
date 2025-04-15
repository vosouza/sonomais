<?php

namespace Vosouza\Sonomais\view\dashboard;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Vosouza\Sonomais\view\ViewInterface;

class DashboardView implements ViewInterface
{
    private Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader('../src/view/dashboard');
        $this->twig = new Environment($loader);
    }

    public function show(array $data = []): void
    {
        // $product = [];
        echo $this->twig->render('dashboard.html.twig', []);
    }

}