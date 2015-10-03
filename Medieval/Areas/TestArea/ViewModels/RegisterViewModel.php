<?php

namespace Medieval\Areas\TestArea\ViewModels;

class RegisterViewModel {

    private $success;
    private $error;

    public function getSuccess() {
        return $this->success;
    }

    public function setSuccess( $success ) {
        $this->success = $success;
    }

    public function getError() {
        return $this->error;
    }

    public function setError( $error ) {
        $this->error = $error;
    }

}