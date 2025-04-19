<?php

declare(strict_types=1);

require_once '../vendor/autoload.php';

use Vosouza\Sonomais\data\repository\{
    ProductRepository,
    UserRepository
};
use Vosouza\Sonomais\view\login\LoginView;
use Vosouza\Sonomais\view\dashboard\DashboardView;
use Vosouza\Sonomais\view\home\HomeView;
use Vosouza\Sonomais\SessionRegistry;
use Vosouza\Sonomais\data\{
    DataSource,
    SQLiteDataSource
};

use Vosouza\Sonomais\controller\{
    HomeViewController,
    DashViewController,
    LoginViewController,
};


SessionRegistry::initialize();

// $dataSource = new SQLiteDataSource();
$dataSource = new DataSource();

$pathInfo = $_SERVER['PATH_INFO'] ?? '/';
$parte_a_remover = '/public_html';
$pathInfo = str_replace($parte_a_remover, '', $pathInfo);

$controller = null;

try{
    if ($pathInfo === '/') {

        $repo = new ProductRepository(source: $dataSource);
        $view = new HomeView();
        $controller = new HomeViewController(view: $view, productRepository: $repo);
        
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
    var_dump(value: $e->getMessage());
}