<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';;

use Vosouza\Sonomais\data\repository\{
    ProductRepository,
    UserRepository
};
use Vosouza\Sonomais\view\login\LoginView;
use Vosouza\Sonomais\view\dashboard\DashboardView;
use Vosouza\Sonomais\view\details\DetailsView;
use Vosouza\Sonomais\view\list\ProductList;
use Vosouza\Sonomais\view\home\HomeView;
use Vosouza\Sonomais\{
    SessionRegistry,
    SonoLogger,
};
use Vosouza\Sonomais\data\{
    DataSource,
    SQLiteDataSource,
    DataSourceTest,
};

use Vosouza\Sonomais\controller\{
    HomeViewController,
    DashViewController,
    LoginViewController,
    DetailsViewController,
    ProductListController
};


SessionRegistry::initialize();
SonoLogger::initialize();

$baseURL = '';
$dataSource;
if ($_SERVER['SERVER_NAME'] === 'localhost'){
    $dataSource = new DataSourceTest();
    $baseURL = 'http://localhost:8080/';
}else{
    $dataSource = new DataSource();
    $baseURL = 'https://darkgreen-moose-358229.hostingersite.com/';
}

$pathInfo = $_SERVER['PATH_INFO'] ?? '/';
$parte_a_remover = '/public_html';
$pathInfo = str_replace($parte_a_remover, '', $pathInfo);
$controller = null;

// print_r($_GET);
// SonoLogger::log('PATH_INFO = '.$_SERVER['PATH_INFO']);
// SonoLogger::log('GET = '.$_GET['productid']);
// SonoLogger::log('_PUT = '.$_PUT['productid']);
// SonoLogger::log('_POST = '.$_POST['productid']);
// SonoLogger::log('_SERVER = '.var_export($_SERVER,true));
try{
    if ($pathInfo === '/') {

        $repo = new ProductRepository(source: $dataSource);
        $view = new HomeView();
        $controller = new HomeViewController(view: $view, productRepository: $repo, baseUrl: $baseURL);

    }else if($pathInfo === '/details'){

        $repo = new ProductRepository(source: $dataSource);
        $view = new DetailsView();
        $controller = new DetailsViewController(view: $view, productRepository: $repo);
        
    }else if($pathInfo === '/colchoes'){

        $repo = new ProductRepository(source: $dataSource);
        $view = new ProductList($baseURL, "Colchões");
        $controller = new ProductListController(view: $view, productRepository: $repo, type: "colchoes");
        
    }else if($pathInfo === '/estofados'){

        $repo = new ProductRepository(source: $dataSource);
        $view = new ProductList($baseURL, "Estofados");
        $controller = new ProductListController(view: $view, productRepository: $repo, type: "estofados");
        
    }else if($pathInfo === '/sobre'){

        $repo = new ProductRepository(source: $dataSource);
        $view = new DetailsView();
        $controller = new DetailsViewController(view: $view, productRepository: $repo);
        
    }else if($pathInfo === '/dash'){

        if(SessionRegistry::isLoggedIn() == false){
            header('Location: /login', true, 303);
            exit();
        }
            
        $repo = new ProductRepository(source: $dataSource);
        $view = new DashboardView();
        $controller = new DashViewController(view: $view, repository: $repo);
        
    }else if($pathInfo === '/dash/create'){

        if(SessionRegistry::isLoggedIn() == false){
            header('Location: /login', true, 303);
            exit();
        }
            
        $repo = new ProductRepository(source: $dataSource);
        $view = new DashboardView();
        $controller = new DashViewController(view: $view, repository: $repo);
        $controller->createProduct();
        
    }else if($pathInfo === '/dash/delete'){

        if(SessionRegistry::isLoggedIn() == false){
            header('Location: /login', true, 303);
            exit();
        }
            
        $repo = new ProductRepository(source: $dataSource);
        $view = new DashboardView();
        $controller = new DashViewController(view: $view, repository: $repo);
        $controller->deleteProduct();
        
    }else if($pathInfo === '/dash/editproduct'){
        SonoLogger::log('passou aqui');
        if(SessionRegistry::isLoggedIn() == false){
            header('Location: /login', true, 303);
            exit();
        }
            
        $repo = new ProductRepository(source: $dataSource);
        $view = new DashboardView();
        $controller = new DashViewController(view: $view, repository: $repo);
        $controller->editProduct();
        
    }else if($pathInfo === '/login'){

        $repo = new UserRepository( $dataSource);
        $view = new LoginView();
        $controller = new LoginViewController($view, $repo);

    }else if($pathInfo === '/logout'){

        SessionRegistry::setLogginIn(false);
        header('location: /', true, 303);
    } else {
        // Lógica para rota não encontrada (ex: definir um controlador de erro 404)
        header("HTTP/1.0 404 Not Found");
        echo "Página não encontrada.";
        exit;
    }

    if ($controller) {
        $controller->processaRequisicao();
    }


}catch(Exception $e){
    SonoLogger::log('_SERVER = '.var_export($e, true));
}