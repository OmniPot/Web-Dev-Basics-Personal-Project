<?php

namespace Medieval\Controllers;

use Medieval\Areas\TestArea\Repositories\UserRepository;
use Medieval\ViewModels\WelcomeViewModel;

class HomeController extends BaseController {

    /**
     * @authorize
     * @customRoute('home/welcome')
     */
    public function welcome() {
        $repo = new UserRepository( $this->_databaseInstance );
        $userInfo = $repo->getInfo( $_SESSION[ 'id' ] );

        $viewModel = new WelcomeViewModel();
        $viewModel->setUsername( $userInfo[ 'username' ] );

        $this->_view->appendToLayout( 'layouts.home', 'header' );
        $this->_view->appendToLayout( 'layouts.home', 'body', $viewModel );
        $this->_view->appendToLayout( 'layouts.home', 'footer' );
        $this->_view->render( 'layouts.home' );
    }
}