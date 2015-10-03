<?php

namespace Medieval\Areas\TestArea\Repositories;

use Medieval\Framework\BaseRepository;

class UserRepository extends BaseRepository {

    const DEFAULT_USER_ROLE_ID = 1;

    public function register( $username, $password ) {
        if ( $this->exists( $username ) ) {
            throw new \Exception( 'Username already taken' );
        }

        $registerUserQuery =
            "INSERT INTO users(username, password, roleId)  VALUES(?, ?, ?)";
        $result = $this->databaseInstance->prepare( $registerUserQuery );

        $result->execute( [
            $username,
            password_hash( $password, PASSWORD_DEFAULT ),
            self::DEFAULT_USER_ROLE_ID
        ] );

        if ( $result->rowCount() > 0 ) {
            $this->login( $username, $password );
        } else {
            throw new \Exception( 'Unsuccessful registration' );
        }
    }

    public function exists( $username ) {
        $findUserQuery = "SELECT id FROM users WHERE username = ?";
        $result = $this->databaseInstance->prepare( $findUserQuery );
        $result->execute( [ $username ] );

        return $result->rowCount() > 0;
    }

    /**
     * @param $username
     * @param $password
     * @return UserRepository
     * @throws \Exception
     */
    public function login( $username, $password ) {
        $query =
            "SELECT
                u.id,
                u.password,
                r.name as 'role'
            FROM users u
            JOIN roles r
                ON r.id = u.roleId
            WHERE username = ?";

        $result = $this->databaseInstance->prepare( $query );
        $result->execute( [ $username ] );

        if ( $result->rowCount() > 0 ) {
            $userRow = $result->fetch();

            if ( password_verify( $password, $userRow[ 'password' ] ) ) {
                return [
                    'id' => $userRow[ 'id' ],
                    'role' => $userRow[ 'role' ]
                ];
            }

            throw new \Exception( 'Wrong password' );
        }

        throw new \Exception( 'Login failed' );
    }

    public function getInfo( $userId ) {
        $query = "SELECT id, username, email, password FROM users WHERE id = ?";
        $result = $this->databaseInstance->prepare( $query );

        $result->execute( [ $userId ] );

        return $result->fetch();
    }

    public function edit( $newUsername, $newPassword, $id ) {
        $updateQuery = "UPDATE users SET password = ?, username = ? WHERE id = ?";
        $result = $this->databaseInstance->prepare( $updateQuery );

        $result->execute( [
            $newUsername,
            password_hash( $newPassword, PASSWORD_DEFAULT ),
            $id
        ] );

        return $result->rowCount() > 0;
    }
}