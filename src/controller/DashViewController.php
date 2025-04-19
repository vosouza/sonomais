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

    private ?Product $editProduct = null;

    public function __construct(ViewInterface $view, ProductRepository $repository){
        $this->productRepository = $repository;
        $this->view = $view;

        if($this->isDeleteProduct()){
            echo 'zasdasd';
            $deleteID = filter_input(INPUT_POST, "deleteId", FILTER_VALIDATE_INT);

            if($this->productRepository->delete($deleteID)){
                echo "Produto excluido";
            }else{
                echo "erro ao excluir produto";
            }
            // header('Location: /dash', true, 303);

        }else if($this->isPostCreateProduct()){
            $product = Product::fromPost();

            if ($product) {
                $this->productRepository->insert(product: $product);
            } else {
                echo "Erro ao processar o formulário.";
            }
            // header('Location: /dash', true, 303);
        }
        
        // else if($this->isGetEditProduct()){
        //     $productId = filter_input(INPUT_GET, "productId", FILTER_VALIDATE_INT);
        //     $product = $this->productRepository->getById($productId);
        //     if ($product != null) {
        //         $editProduct = $product;
        //     }else{
        //         echo "produto nao encontrado";
        //     }
        // }
    }

    public function processaRequisicao(): void{
        $products = $this->productRepository->findAll();
        $this->view->show(["products"=> $products]);
    }

    public function isPostCreateProduct(): bool {
        return $_SERVER['REQUEST_METHOD'] !== 'POST';
    }

    public function isDeleteProduct(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteId']);
    }

    public function isGetEditProduct(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['prudctId']);
    }
}