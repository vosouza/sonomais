<?php

namespace Vosouza\Sonomais\view\list;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Vosouza\Sonomais\view\ViewInterface;

class ProductList implements ViewInterface
{
    private Environment $twig;
    private String $baseUrl;
    private String $listName;

    public function __construct(String $baseUrl, String $listName)
    {
        $this->listName = $listName;
        $this->baseUrl = $baseUrl;
        $loader = new FilesystemLoader([__DIR__, __DIR__.'/../templates'] ); // Caminho para seus templates
        $this->twig = new Environment($loader);
    }

    public function show(array $data = []): void
    {
        echo $this->twig->render('list.html.twig', ['products' => $data, 'base' => $this->baseUrl, 'listName'=> $this->listName]);
    }

}