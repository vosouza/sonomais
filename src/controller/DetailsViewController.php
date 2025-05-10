<?php

declare(strict_types=1);

namespace Vosouza\Sonomais\controller;

use Vosouza\Sonomais\data\repository\ProductRepository;
use Vosouza\Sonomais\view\ViewInterface;

class DetailsViewController implements Controller{

    private ViewInterface $view;
    private ProductRepository $productRepository;
    private int $productID = 0;

    public function __construct(ViewInterface $view, ProductRepository $productRepository){
        $this->view = $view;
        $this->productRepository = $productRepository;
        $this->getProductID();
    }

    public function setProductId($productID): void{   
        $this->productID = $productID;
    }

    public function getProductID(): void{
        if(!isset($_GET["productid"])){
            header('Location: /', true, 303);
            exit();
        }

        $this->productID = filter_input(INPUT_GET, 'productid', FILTER_VALIDATE_INT);
    }
    public function processaRequisicao(): void{
        $showMore = $this->productRepository->findAll();
        $product = $this->productRepository->getById($this->productID);
        $this->view->show(["product" => $product, "showMore"=> $showMore]);
    }
}