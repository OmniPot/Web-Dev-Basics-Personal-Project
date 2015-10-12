<?php

namespace Medieval\Areas\ProfileArea\Controllers;

use Medieval\Areas\TestArea\Repositories\UserRepository;
use Medieval\Controllers\BaseController;
use Medieval\Areas\ProfileArea\ViewModels\ProfileViewModel;

class ProfileController extends BaseController {

    /**
     * @authorize
     * @customRoute('profile/me')
     */
    public function myProfile() {
        $repo = new UserRepository( $this->_databaseInstance );
        $userInfo = $repo->getInfo( $_SESSION[ 'id' ] );

        $viewModel = new ProfileViewModel();
        $viewModel->setUsername( $userInfo[ 'username' ] );

        $this->_view->appendToLayout( 'layouts.profile', 'profile.myProfile', $viewModel );
        $this->_view->render( 'layouts.profile' );
    }
}