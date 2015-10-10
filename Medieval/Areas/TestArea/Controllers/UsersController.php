<?php

namespace Medieval\Areas\TestArea\Controllers;

use Medieval\Framework\BaseController;
use Medieval\Framework\View;

use Medieval\Areas\TestArea\BindingModels\LoginBindingModel;
use Medieval\Areas\TestArea\BindingModels\RegisterBindingModel;
use Medieval\Areas\TestArea\Repositories\UserRepository;

use Medieval\Areas\TestArea\ViewModels\RegisterViewModel;
use Medieval\Areas\TestArea\ViewModels\LoginViewModel;

class UsersController extends BaseController {

    /**
     * @method POST
     * @customRoute('user/login')
     * @param LoginBindingModel $model
     * @return View
     */
    public function login( LoginBindingModel $model ) {
        $username = $model->username;
        $password = $model->password;

        $this->initLogin( $username, $password );
    }

    /**
     * @customRoute('user/login')
     * @return View
     */
    public function loginPage() {
        if ( $this->isLogged() ) {
            $this->redirect( $this->alreadyAuthorizedLocation );
        }

        $viewModel = new LoginViewModel();
        return new View( $viewModel );
    }

    /**
     * @method POST
     * @customRoute('user/register')
     * @param RegisterBindingModel $model
     * @throws \Exception
     */
    public function register( RegisterBindingModel $model ) {
        $username = $model->username;
        $password = $model->password;
        $confirm = $model->confirm;

        $name = isset( $model->name ) ? $model->name : null;

        if ( $password != $confirm ) {
            throw new \Exception( 'Password and confirmation do not match' );
        }

        $userModel = new UserRepository( $this->databaseInstance );
        $userModel->register( $username, $password );

        $this->initLogin( $username, $password );
    }

    /**
     * @customRoute('user/register')
     */
    public function registerPage() {
        if ( $this->isLogged() ) {
            $this->redirect( $this->alreadyAuthorizedLocation );
        }

        $viewModel = new RegisterViewModel();
        return new View( $viewModel );
    }

    /**
     * @method POST
     * @customRoute('user/logout')
     */
    public function logout() {
        session_destroy();
        $this->redirect( $this->unauthorizedLocation );
        exit;
    }

    private function initLogin( $username, $password ) {
        $userModel = new UserRepository( $this->databaseInstance );

        $userInfo = $userModel->login( $username, $password );

        $_SESSION = [ ];
        $_SESSION[ 'id' ] = $userInfo[ 'id' ];
        $_SESSION[ 'role' ] = $userInfo[ 'role' ];

        $this->redirect( $this->alreadyAuthorizedLocation );
    }
}