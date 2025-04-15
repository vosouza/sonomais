<?php

declare(strict_types=1);

require_once '../vendor/autoload.php';

use Vosouza\Sonomais\data\{
    DataSource,
    SQLiteDataSource
};

use Vosouza\Sonomais\data\repository\{
    ProductRepository,
};

use Vosouza\Sonomais\controller\{
    HomeViewController,
    DashViewController
};


use Vosouza\Sonomais\view\home\{
    HomeView,
};

$dataSource = new SQLiteDataSource();

try{
    if (!array_key_exists('PATH_INFO', $_SERVER) || $_SERVER['PATH_INFO'] === '/') {
        
        $repo = new ProductRepository($dataSource);
        $view = new HomeView();
        $controller = new HomeViewController($view, $repo);
        
    }else if(!array_key_exists('PATH_INFO', $_SERVER) || $_SERVER['PATH_INFO'] === '/dash'){
        $repo = new ProductRepository($dataSource);
        $controller = new DashViewController($repo);
    }


}catch(Exception $e){
    var_dump(value: $e->getMessage());
}

$controller->processaRequisicao();