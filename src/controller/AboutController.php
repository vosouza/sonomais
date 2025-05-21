<?php

declare(strict_types=1);

namespace Vosouza\Sonomais\controller;

use Vosouza\Sonomais\data\repository\ProductRepository;
use Vosouza\Sonomais\SessionRegistry;
use Vosouza\Sonomais\view\ViewInterface;

class AboutController implements Controller{

    private ViewInterface $view;
    private String $baseUrl;

    public function __construct(ViewInterface $view, String $baseUrl){
        
        $this->baseUrl = $baseUrl;
        $this->view = $view;
    }

    public function processaRequisicao(): void{
        $this->view->show(data: [
            'base' => $this->baseUrl,
            'appversion'=>SessionRegistry::$appVersion
        ]);
    }
}