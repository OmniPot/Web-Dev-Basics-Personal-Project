<?php

namespace Medieval\Controllers;

use Medieval\Areas\TestArea\Repositories\UserRepository;
use Medieval\Framework\BaseController;
use Medieval\Framework\View;

use Medieval\ViewModels\WelcomeViewModel;

class HomeController extends BaseController {

    /**
     * @route('home/welcome')
     */
    public function welcome() {
        if ( !$this->isLogged() ) {
            $this->redirect( $this->unauthorizedLocation );
        }

        $repo = new UserRepository( $this->databaseInstance );
        $userInfo = $repo->getInfo( $_SESSION[ 'id' ] );

        $viewModel = new WelcomeViewModel();
        $viewModel->setUsername( $userInfo[ 'username' ] );

        return new View( $viewModel );
    }
}