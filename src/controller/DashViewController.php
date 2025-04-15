<?php

declare(strict_types=1);

namespace Vosouza\Sonomais\controller;

use Vosouza\Sonomais\data\repository\ProductRepository;
use Vosouza\Sonomais\view\ViewInterface;
use Vosouza\Sonomais\model\{
    Product,
};

class DashViewController implements Controller{

    
    private ViewInterface $view;
    private $productRepository;

    public function __construct(ViewInterface $view, ProductRepository $repository){
        $this->productRepository = $repository;
        $this->view = $view;

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $product = Product::fromPost();

            if ($product) {
                $this->productRepository->insert($product);
            } else {
                echo "Erro ao processar o formulário.";
            }
        }
    }

    public function processaRequisicao(): void{
        $this->view->show();
    }
}