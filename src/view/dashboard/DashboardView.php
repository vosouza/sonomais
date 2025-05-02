<?php

namespace Vosouza\Sonomais\view\dashboard;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Vosouza\Sonomais\view\ViewInterface;

class DashboardView implements ViewInterface
{
    private Environment $twig;
    private String $viewFile;

    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__);
        $this->viewFile = 'dashboard.html.twig';
        $this->twig = new Environment($loader);
    }

    public function renderEdit (): void{
        $this->viewFile = 'edit.html.twig';
    }

    public function show(array $data = []): void
    {
        // $product = [];
        echo $this->twig->render($this->viewFile, $data);
    }

}