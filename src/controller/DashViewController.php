<?php

declare(strict_types=1);

namespace Vosouza\Sonomais\controller;

use Vosouza\Sonomais\data\repository\ProductRepository;
use Vosouza\Sonomais\model\{
    Product,
};

class DashViewController implements Controller{

    private $productRepository;

    public function __construct(ProductRepository $repository){
        $this->productRepository = $repository;
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
        include_once __DIR__ .'/../../dashboard/dash.php';
    }

    public function setHead(): void{
        
    }

    public function setFooter(): void{

    }

}