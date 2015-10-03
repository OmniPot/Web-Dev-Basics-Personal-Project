<?php

namespace Medieval\Areas\TestArea\Controllers;

use Medieval\Areas\TestArea\BindingModels\LoginBindingModel;
use Medieval\Areas\TestArea\BindingModels\RegisterBindingModel;
use Medieval\Framework\BaseController;
use Medieval\Framework\View;

use Medieval\Areas\TestArea\Repositories\UserRepository;

use Medieval\Areas\TestArea\ViewModels\RegisterViewModel;
use Medieval\Areas\TestArea\ViewModels\LoginViewModel;

class UsersController extends BaseController {

    /**
     * @method GET
     * @route('user/login')
     * @return View
     * @throws \Exception
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
     * @route('user/login')
     * @param LoginBindingModel $model
     * @return View
     */
    public function login( LoginBindingModel $model ) {
        if ( $this->isLogged() ) {
            $this->redirect( $this->alreadyAuthorizedLocation );
        }

        $username = $model->getUsername();
        $password = $model->getPassword();

        $this->initLogin( $username, $password );
    }

    /**
     * @method POST
     * @route('user/register')
     * @param RegisterBindingModel $model
     * @throws \Exception
     */
    public function register( RegisterBindingModel $model ) {
        if ( $this->isLogged() ) {
            $this->redirect( $this->unauthorizedLocation );
        }

        $username = $model->getUsername();
        $password = $model->getPassword();
        $confirm = $model->getConfirm();
        $name = $model->getName();

        if ( $password != $confirm ) {
            throw new \Exception( 'Password and confirmation do not match' );
        }

        $userModel = new UserRepository( $this->databaseInstance );
        $userModel->register( $username, $password );

        $this->initLogin( $username, $password );
    }

    /**
     * @method GET
     * @route('user/register')
     * @return View
     */
    public function registerPage() {
        if ( $this->isLogged() ) {
            $this->redirect( $this->unauthorizedLocation );
        }

        $viewModel = new RegisterViewModel();
        return new View( $viewModel );
    }

    /**
     * @method POST
     * @route('user/logout')
     */
    public function logout() {
        session_destroy();
        $this->redirect( $this->unauthorizedLocation );
        die;
    }

    private function initLogin( $username, $password ) {
        $userModel = new UserRepository( $this->databaseInstance );

        $userId = $userModel->login( $username, $password );

        $_SESSION = [ ];
        $_SESSION[ 'id' ] = $userId;

        $this->redirect( $this->alreadyAuthorizedLocation );
    }
}