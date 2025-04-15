<?php

declare(strict_types=1);

namespace Vosouza\Sonomais\controller;

use Vosouza\Sonomais\data\repository\ProductRepository;
use Vosouza\Sonomais\model\Product;
use Vosouza\Sonomais\view\home\HomeView;
use Vosouza\Sonomais\view\ViewInterface;

class HomeViewController implements Controller{

    private ViewInterface $view;
    private ProductRepository $productRepository;

    public function __construct(ViewInterface $view, ProductRepository $productRepository){
        $this->view = $view;
        $this->productRepository = $productRepository;
    }

    public function processaRequisicao(): void{
        $product = $this->productRepository->findAll();
        $this->view->show($product);
    }
}