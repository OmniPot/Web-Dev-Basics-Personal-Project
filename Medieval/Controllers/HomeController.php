<?php

namespace Medieval\Controllers;

use Medieval\Areas\TestArea\Repositories\UserRepository;
use Medieval\Framework\View;

use Medieval\ViewModels\WelcomeViewModel;

class HomeController extends BaseController {

    /**
     * @authorize
     * @customRoute('home/welcome')
     */
    public function welcome() {
        $repo = new UserRepository( $this->databaseInstance );
        $userInfo = $repo->getInfo( $_SESSION[ 'id' ] );

        $viewModel = new WelcomeViewModel();
        $viewModel->setUsername( $userInfo[ 'username' ] );

        return new View( $viewModel );
    }
}