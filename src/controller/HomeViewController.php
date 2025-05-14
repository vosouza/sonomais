<?php

declare(strict_types=1);

namespace Vosouza\Sonomais\controller;

use Vosouza\Sonomais\data\repository\ProductRepository;
use Vosouza\Sonomais\SessionRegistry;
use Vosouza\Sonomais\view\ViewInterface;

class HomeViewController implements Controller{

    private ViewInterface $view;
    private ProductRepository $productRepository;
    private String $baseUrl;
    private int $sectionIndexNumber = 0;

    public function __construct(ViewInterface $view, ProductRepository $productRepository, String $baseUrl){
        
        $this->baseUrl = $baseUrl;
        $this->view = $view;
        $this->productRepository = $productRepository;
    }

    public function processaRequisicao(): void{
        $product = $this->productRepository->findAllFeatured();
        $featured = $this->productRepository->getById(3);
        $this->view->show(data: [
            'products' => $product,
            'base' => $this->baseUrl,
            'featuredProduct'=>$product[0],
            'appversion'=>SessionRegistry::$appVersion
        ]);
    }
}