<?php

namespace Zf2menu;

return array (
    'controllers'  => array (
        'invokables' => array (
            'Zf2menu\Controller\Menus' => 'Zf2menu\Controller\MenusController',
        ),
    ),
    'router'       => array (
        'routes' => array (
            'menus'         => array (
                'type'          => 'segment',
                'options'       => array (
                    'route'    => '/menus',
                    'defaults' => array (
                        'controller' => 'Zf2menu\Controller\Menus',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array (
                    'search'  => array (
                        'type'    => 'segment',
                        'options' => array (
                            'route'       => '/search[/:id][/page/:page][/item_per_page/:item_per_page][/order_by/:order_by][/:order][/search_by/:search_by]',
                            'constraints' => array (
                                'id'       => '[0-9]+',
                                'page'     => '[0-9]+',
                                'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'order'    => 'ASC|DESC',
                            ),
                            'defaults'    => array (
                                'action' => 'search',
                            ),
                        ),
                    ),
                    'index'   => array (
                        'type'    => 'segment',
                        'options' => array (
                            'route'       => '/index[/:id][/page/:page][/item_per_page/:item_per_page][/order_by/:order_by][/:order][/search_by/:search_by]',
                            'constraints' => array (
                                'id'       => '[0-9]+',
                                'page'     => '[0-9]+',
                                'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'order'    => 'ASC|DESC',
                            ),
                            'defaults'    => array (
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'add'     => array (
                        'type'    => 'segment',
                        'options' => array (
                            'route'    => '/add',
                            'defaults' => array (
                                'action' => 'add',
                            ),
                        ),
                    ),
                    'edit'    => array (
                        'type'    => 'segment',
                        'options' => array (
                            'route'       => '/edit[/:id]',
                            'constraints' => array (
                                'id' => '[0-9]+',
                            ),
                            'defaults'    => array (
                                'action' => 'edit',
                            ),
                        ),
                    ),
                    'reorder' => array (
                        'type'    => 'segment',
                        'options' => array (
                            'route'       => '/reorder[/:id][/:direction]',
                            'constraints' => array (
                                'id'        => '[0-9]+',
                                'direction' => 'up|down',
                            ),
                            'defaults'    => array (
                                'action' => 'reorder',
                            ),
                        ),
                    ),
                    'delete'  => array (
                        'type'    => 'segment',
                        'options' => array (
                            'route'       => '/delete[/:id]',
                            'constraints' => array (
                                'id'       => '[0-9]+',
                                'page'     => '[0-9]+',
                                'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'order'    => 'ASC|DESC',
                            ),
                            'defaults'    => array (
                                'action' => 'delete',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array (
        'template_path_stack' => array (
            'menus' => __DIR__ . '/../view',
        ),
    ),
);
