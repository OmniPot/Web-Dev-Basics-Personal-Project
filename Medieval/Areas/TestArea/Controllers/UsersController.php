<?php

namespace Medieval\Areas\TestArea\Controllers;

use Medieval\Areas\TestArea\BindingModels\LoginBindingModel;
use Medieval\Areas\TestArea\BindingModels\RegisterBindingModel;
use Medieval\Areas\TestArea\Repositories\UserRepository;
use Medieval\Areas\TestArea\ViewModels\LoginViewModel;
use Medieval\Areas\TestArea\ViewModels\RegisterViewModel;
use Medieval\Controllers\BaseController;
use Medieval\Framework\View;

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
     */
    public function loginPage() {
        if ( $this->isLogged() ) {
            $this->redirect( $this->alreadyAuthorizedLocation );
        }

        $viewModel = new LoginViewModel();

        $this->_view->appendToLayout( 'layouts.login', 'users.loginPage', $viewModel );
        $this->_view->render( 'layouts.login' );
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

        $userModel = new UserRepository( $this->_databaseInstance );
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

        $this->_view->appendToLayout( 'layouts.register', 'users.registerPage', $viewModel );
        $this->_view->render( 'layouts.register' );
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
        $userModel = new UserRepository( $this->_databaseInstance );

        $userInfo = $userModel->login( $username, $password );

        $_SESSION = [ ];
        $_SESSION[ 'id' ] = $userInfo[ 'id' ];
        $_SESSION[ 'role' ] = $userInfo[ 'role' ];

        $this->redirect( $this->alreadyAuthorizedLocation );
    }
}