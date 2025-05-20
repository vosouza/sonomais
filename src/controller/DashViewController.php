<?php

declare(strict_types=1);

namespace Vosouza\Sonomais\controller;

use Vosouza\Sonomais\data\repository\ProductRepository;
use Vosouza\Sonomais\view\dashboard\DashboardView;
use Vosouza\Sonomais\model\{
    Product,
};
use Vosouza\Sonomais\{
    SonoLogger,
    SessionRegistry
};

class DashViewController implements Controller{

    
    private DashboardView $view;
    private $productRepository;

    private ?Product $editProduct = null;

    public function __construct(DashboardView $view, ProductRepository $repository){
        $this->productRepository = $repository;
        $this->view = $view;
    }

    public function createProduct(){
        if($this->isPostCreateProduct()){

            $product = Product::fromPost();
            var_dump($product);

            if ($product) {
                $this->productRepository->insert(product: $product);
            } else {
                SonoLogger::log( "Erro ao processar o formulário.");
            }
        }
        header('Location: /dash', true, 303);
    }

    public function editProduct(){
        $this->view->renderEdit();
        $product = Product::editFromPost();
        
        
        if($product == null){
            $productId = filter_input(INPUT_GET, "productid", FILTER_VALIDATE_INT);
            $product = $this->productRepository->getById($productId);
            if ($product != null) {
                $this->editProduct = $product;
            }else{
                SonoLogger::log( "produto nao encontrado");
            }
        }else{
            $dbProduct = $this->productRepository->getById(id: $product->id);

            $newProduct = new  Product(
                    $product->id,
                    $product->name == '' ? $dbProduct->name : $product->name,
                    $product->description == '' ? $dbProduct->description : $product->description,
                    $product->price == 0 ? $dbProduct->price : $product->price,
                    $product->type == '' ? $dbProduct->type : $product->type,
                    $product->image == '' ? $dbProduct->image : $product->image ,
                    $product->isStar,
                    []
                );

            $this->productRepository->update(product: $newProduct, id: $product->id );
            
            if ($product != null) {
                $this->editProduct = $product;
            }else{
                SonoLogger::log( "produto nao encontrado");
            }
        }
        
    }

    public function deleteProduct(){
        if(!($_SERVER['REQUEST_METHOD'] === 'POST') && !isset($_POST['deleteId'])){
            SonoLogger::log( "Erro request method");
            exit();
        }

        $deleteID = filter_input(INPUT_POST, "deleteId", FILTER_VALIDATE_INT);

        if($this->productRepository->delete($deleteID)){
            SonoLogger::log( "Produto excluido");
        }else{
            SonoLogger::log( "erro ao excluir produto");
        }
        header('Location: /dash', true, 303);
    }

    public function processaRequisicao(): void{
        if($this->editProduct == null){
            $products = $this->productRepository->findAll();
            SonoLogger::log(var_export($products, true));
            $this->view->show(["products"=> $products, "cssversion"=>SessionRegistry::$appVersion]);
        }else{
            $this->view->show(["product"=> $this->editProduct, "cssversion"=>SessionRegistry::$appVersion]);
        }
    }

    public function isPostCreateProduct(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public function isDeleteProduct(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteId']);
    }

    public function isGetEditProduct(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['prudctId']);
    }
}