<?php

declare(strict_types=1);

namespace Vosouza\Sonomais\controller;

use Vosouza\Sonomais\data\repository\ProductRepository;
use Vosouza\Sonomais\SonoLogger;
use Vosouza\Sonomais\view\ViewInterface;

class ProductListController implements Controller{

    private ViewInterface $view;
    private ProductRepository $productRepository;
    private String $type;

    public function __construct(ViewInterface $view, ProductRepository $productRepository, String $type){
        $this->type = $type;
        $this->view = $view;
        $this->productRepository = $productRepository;
    }

    public function processaRequisicao(): void{
        SonoLogger::log($this->type);
        $product = $this->productRepository->findByType($this->type);
        SonoLogger::log(print_r($product, true));
        $this->view->show($product);
    }
}