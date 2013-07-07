<?php

namespace ZfcUserExtend;

return array(
    'controllers' => array(
        'invokables' => array(
            'ZfcUserExtend\Controller\Role' => 'ZfcUserExtend\Controller\RoleController',
            'ZfcUserExtend\Controller\UserAdmin' => 'ZfcUserExtend\Controller\UserAdminController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'role' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/role[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'ZfcUserExtend\Controller\Role',
                        'action'     => 'index',
                    ),
                ),
            ),
            'useradmin' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/user[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'ZfcUserExtend\Controller\UserAdmin',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'zfc-user-extend' => __DIR__ . '/../view',
        ),
    ),

    // Doctrine config
    'doctrine' => array(
        'driver' => array(
            'zfcuser_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                //'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'),
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => 'zfcuser_entity',
                ),
            ),
        ),
    ),

    'zfcuser' => array(
        // telling ZfcUser to use our own class
        'user_entity_class'       => 'ZfcUserExtend\Entity\User',
        // telling ZfcUserDoctrineORM to skip the entities it defines
        'enable_default_entities' => false,
    ),

    'bjyauthorize' => array(
        // Using the authentication identity provider, which basically reads the roles from the auth service's identity
        'identity_provider' => 'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider',

        'role_providers'        => array(
            // using an object repository (entity repository) to load all roles into our ACL
            'BjyAuthorize\Provider\Role\ObjectRepositoryProvider' => array(
                'object_manager'    => 'doctrine.entitymanager.orm_default',
                'role_entity_class' => 'ZfcUserExtend\Entity\Role',
             ),
        ),
    ),
);
