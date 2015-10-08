<?php

$expires = '2015-10-08 16:44:59';

$appStructure = [
    'Profile' => [
        'Medieval\Areas\ProfileArea\Controllers\ProfileController' => [
            'myProfile' => [
                'route' => [
                    'uri' => 'profile/me',
                    'params' => [ ],

                ],
                'method' => 'GET',
                'authorize' => '1',
                'admin' => '',
                'defaultRoute' => 'profile/profile/myProfile',

            ],
            '__construct' => [ ],

        ],

    ],
    'Main' => [
        'Medieval\Controllers\HomeController' => [
            'welcome' => [ ],
            '__construct' => [ ],

        ],

    ],
    'Test' => [
        'Medieval\Areas\TestArea\Controllers\UsersController' => [
            'login' => [
                'route' => [
                    'uri' => 'user/login',
                    'params' => [ ],

                ],
                'method' => 'POST',
                'admin' => '',
                'authorize' => '',
                'defaultRoute' => 'test/users/login',

            ],
            'loginPage' => [
                'route' => [
                    'uri' => 'user/login',
                    'params' => [ ],

                ],
                'method' => 'GET',
                'admin' => '',
                'authorize' => '',
                'defaultRoute' => 'test/users/loginPage',

            ],
            'register' => [
                'route' => [
                    'uri' => 'user/register',
                    'params' => [ ],

                ],
                'method' => 'POST',
                'admin' => '',
                'authorize' => '',
                'defaultRoute' => 'test/users/register',

            ],
            'registerPage' => [
                'route' => [
                    'uri' => 'user/register',
                    'params' => [ ],

                ],
                'method' => 'GET',
                'admin' => '',
                'authorize' => '',
                'defaultRoute' => 'test/users/registerPage',

            ],
            'logout' => [
                'route' => [
                    'uri' => 'user/logout',
                    'params' => [ ],

                ],
                'method' => 'POST',
                'admin' => '',
                'authorize' => '',
                'defaultRoute' => 'test/users/logout',

            ],
            '__construct' => [ ],

        ],

    ],

];


$actionsStructure = [
    'myProfile' => [
        'route' => [
            'uri' => 'profile/me',
            'params' => [ ],

        ],
        'method' => 'GET',
        'authorize' => '1',
        'admin' => '',
        'defaultRoute' => 'profile/profile/myProfile',

    ],
    '__construct' => [ ],
    'welcome' => [ ],
    'login' => [
        'route' => [
            'uri' => 'user/login',
            'params' => [ ],

        ],
        'method' => 'POST',
        'admin' => '',
        'authorize' => '',
        'defaultRoute' => 'test/users/login',

    ],
    'loginPage' => [
        'route' => [
            'uri' => 'user/login',
            'params' => [ ],

        ],
        'method' => 'GET',
        'admin' => '',
        'authorize' => '',
        'defaultRoute' => 'test/users/loginPage',

    ],
    'register' => [
        'route' => [
            'uri' => 'user/register',
            'params' => [ ],

        ],
        'method' => 'POST',
        'admin' => '',
        'authorize' => '',
        'defaultRoute' => 'test/users/register',

    ],
    'registerPage' => [
        'route' => [
            'uri' => 'user/register',
            'params' => [ ],

        ],
        'method' => 'GET',
        'admin' => '',
        'authorize' => '',
        'defaultRoute' => 'test/users/registerPage',

    ],
    'logout' => [
        'route' => [
            'uri' => 'user/logout',
            'params' => [ ],

        ],
        'method' => 'POST',
        'admin' => '',
        'authorize' => '',
        'defaultRoute' => 'test/users/logout',

    ],

];
