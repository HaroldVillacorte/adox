<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'TeamContact\Controller\TeamContact' => 'TeamContact\Controller\TeamContactController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'team-contact' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/team-contact[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'TeamContact\Controller\TeamContact',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'team-contact' => __DIR__ . '/../view',
        ),
    ),
);
