<?php

namespace Medieval\Areas\ProfileArea\ViewModels;

class ProfileViewModel {

    public $success;
    public $error;

    private $username;

    public function getUsername() {
        return $this->username;
    }

    public function setUsername( $username ) {
        $this->username = $username;
    }
}