<?php

namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

return [
    'routes' => [
        'home'  => [
            'type'    => Literal::class,
            'options' => [
                'route'    => '/',
                'defaults' => [
                    'controller' => Controller\LoginController::class,
                    'action'     => 'login',
                ],
            ],
        ],
        'user'  => [
            'type'          => Literal::class,
            'options'       => [
                'route'    => '/user',
                'defaults' => [
                    'controller' => Controller\UserController::class,
                ],
            ],
            'may_terminate' => false,
            'child_routes'  => [
                'view-profile'     => [
                    'type'    => Literal::class,
                    'options' => [
                        'route'    => '/view',
                        'defaults' => [
                            'action' => 'view-profile',
                        ],
                    ],
                ],
                'edit-profile'     => [
                    'type'    => Literal::class,
                    'options' => [
                        'route'    => '/edit',
                        'defaults' => [
                            'action' => 'edit-profile',
                        ],
                    ],
                ],
                'view-user-list'   => [
                    'type'    => Literal::class,
                    'options' => [
                        'route'    => '/list',
                        'defaults' => [
                            'action' => 'view-user-list',
                        ],
                    ],
                ],
                'view-dialog-list' => [
                    'type'          => Literal::class,
                    'options'       => [
                        'route'    => '/im',
                        'defaults' => [
                            'controller' => Controller\MessengerController::class,
                            'action'     => 'view-dialog-list',
                        ],
                    ],
                    'may_terminate' => true,
                    'child_routes'  => [
                        'view-messages' => [
                            'type'    => Segment::class,
                            'options' => [
                                'route'       => '/:id',
                                'defaults'    => [
                                    'action' => 'view-messages',
                                ],
                                'constraints' => [
                                    'id' => '\d+',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'admin' => [
            'type'          => Literal::class,
            'options'       => [
                'route'    => '/admin',
                'defaults' => [
                    'controller' => Controller\AdminController::class,
                ],
            ],
            'may_terminate' => false,
            'child_routes'  => [
                'view-user-list' => [
                    'type'          => Literal::class,
                    'options'       => [
                        'route'    => '/list',
                        'defaults' => [
                            'action' => 'view-user-list',
                        ],
                    ],
                    'may_terminate' => true,
                    'child_routes'  => [
                        'edit-user' => [
                            'type'    => Segment::class,
                            'options' => [
                                'route'       => '/:id',
                                'defaults'    => [
                                    'action' => 'edit-user',
                                ],
                                'constraints' => [
                                    'id' => '\d+',
                                ],
                            ],
                        ],
                    ],
                ],
                'edit-position'  => [
                    'type'    => Literal::class,
                    'options' => [
                        'route'    => '/positions',
                        'defaults' => [
                            'action' => 'edit-positions',
                        ],
                    ],
                ],
            ],
        ],
    ],
];