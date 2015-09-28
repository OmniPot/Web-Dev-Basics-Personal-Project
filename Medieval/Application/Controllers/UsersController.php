<?php

namespace Medieval\Application\Controllers;

use Medieval\Framework\View;

use Medieval\Application\Models\User;

use Medieval\Application\ViewModels\RegisterViewModel;
use Medieval\Application\ViewModels\LoginViewModel;

class UsersController extends BaseController {

    public function login() {
        if ( $this->isLogged() ) {
            $this->redirect( $this->alreadyAuthorizedLocation );
        }

        $viewModel = new LoginViewModel();

        if ( isset( $_POST[ 'username' ], $_POST[ 'password' ] ) ) {
            try {
                $username = $_POST[ 'username' ];
                $password = $_POST[ 'password' ];

                $this->initLogin( $username, $password );
            } catch ( \Exception $exception ) {
                $viewModel->error = $exception->getMessage();
                return new View( $viewModel );
            }
        }

        return new View();
    }

    public function logout() {
        session_destroy();
        $this->redirect( $this->unauthorizedLocation );
    }

    public function register() {
        if ( $this->isLogged() ) {
            $this->redirect( $this->alreadyAuthorizedLocation );
        }

        $viewModel = new RegisterViewModel();

        if ( isset( $_POST[ 'username' ], $_POST[ 'password' ] ) ) {
            try {
                $username = $_POST[ 'username' ];
                $password = $_POST[ 'password' ];

                $userModel = new User( $this->databaseInstance );
                $userModel->register( $username, $password );

                $this->initLogin( $username, $password );
            } catch ( \Exception $exception ) {
                $viewModel->error = $exception->getMessage();
                return new View( $viewModel );
            }
        }

        return new View();
    }

    private function initLogin( $username, $password ) {
        $userModel = new User( $this->databaseInstance );

        $userId = $userModel->login( $username, $password );

        $_SESSION = [ ];
        $_SESSION[ 'id' ] = $userId;

        $this->redirect( $this->alreadyAuthorizedLocation );
    }
}