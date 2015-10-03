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
     * @method POST
     * @route('user/login')
     * @param LoginBindingModel $model
     * @return View
     */
    public function login( LoginBindingModel $model ) {
        $username = $model->getUsername();
        $password = $model->getPassword();

        $this->initLogin( $username, $password );
    }

    /**
     * @route('user/login')
     * @return View
     */
    public function loginPage() {
        $viewModel = new LoginViewModel();
        return new View( $viewModel );
    }

    /**
     * @method POST
     * @route('user/register')
     * @param RegisterBindingModel $model
     * @throws \Exception
     */
    public function register( RegisterBindingModel $model ) {
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
     * @route('user/register')
     */
    public function registerPage() {
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

        $userInfo = $userModel->login( $username, $password );

        $_SESSION = [ ];
        $_SESSION[ 'id' ] = $userInfo[ 'id' ];
        $_SESSION[ 'role' ] = $userInfo[ 'role' ];

        $this->redirect( $this->alreadyAuthorizedLocation );
    }
}