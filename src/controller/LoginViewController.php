<?php

declare(strict_types=1);

namespace Vosouza\Sonomais\controller;

use Vosouza\Sonomais\data\repository\UserRepository;
use Vosouza\Sonomais\SessionRegistry;
use Vosouza\Sonomais\view\ViewInterface;
use Vosouza\Sonomais\model\User;

class LoginViewController implements Controller{

    private ViewInterface $view;
    private UserRepository $userRepository;

    public function __construct(ViewInterface $view, UserRepository $userRepository){
        $this->view = $view;
        $this->userRepository = $userRepository;
    }

    public function processaRequisicao(): void{
       $this->processLogin();
    }

    public function processLogin(): void
    {
        if ($userLogin = User::fromLoginPost()) {
            echo 'aqui';
            // $userLogin agora é um objeto User com email e senha preenchidos
            $loggedInUser = $this->userRepository->verifyCredentials(
                $userLogin->getEmail(),
                $userLogin->getPassword()
            );
            echo 'aqui';
            if ($loggedInUser) {
                // Login bem-sucedido
                SessionRegistry::setLogginIn(true);
                SessionRegistry::setUserId($loggedInUser->getId());
                header("Location: /dash");
                exit;
            } else {
                // Credenciais inválidas
                $this->view->show(['error' => 'Nome de usuário ou senha incorretos.']);
            }
        } else {
            // Requisição não é POST ou campos vazios
            $this->view->show(['error' => 'Por favor, preencha todos os campos.']);
        }
    }
}