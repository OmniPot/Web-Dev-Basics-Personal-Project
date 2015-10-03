<?php

namespace Medieval\Areas\ProfileArea\Controllers;

use Medieval\Areas\TestArea\Repositories\UserRepository;
use Medieval\Framework\BaseController;
use Medieval\Areas\ProfileArea\ViewModels\ProfileViewModel;
use Medieval\Framework\View;

class ProfileController extends BaseController {

    /**
     * @authorize
     * @method GET
     * @route('profile/me')
     */
    public function myProfile() {
        $repo = new UserRepository( $this->databaseInstance );
        $userInfo = $repo->getInfo( $_SESSION[ 'id' ] );

        $viewModel = new ProfileViewModel();
        $viewModel->setUsername( $userInfo[ 'username' ] );

        return new View( $viewModel );
    }
}