<?php

namespace Medieval\Areas\TestArea\BindingModels;

class RegisterBindingModel {
    private $username;
    private $password;
    private $confirm;
    private $name;

    public function getUsername() {
        return $this->username;
    }

    public function setUsername( $username ) {
        $this->username = $username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword( $password ) {
        $this->password = $password;
    }

    public function getName() {
        return $this->name;
    }

    public function setName( $name ) {
        $this->name = $name;
    }

    public function getConfirm() {
        return $this->confirm;
    }

    public function setConfirm( $confirm ) {
        $this->confirm = $confirm;
    }

}