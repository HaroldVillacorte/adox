<?php

namespace Music;

return array(
    'controllers' => array(
        'invokables' => array(
            'Music\Controller\Music' => 'Music\Controller\MusicController',
            'Music\Controller\Genre' => 'Music\Controller\GenreController',
            'Music\Controller\Artist' => 'Music\Controller\ArtistController',
            'Music\Controller\Album' => 'Music\Controller\AlbumController',
            'Music\Controller\Song' => 'Music\Controller\SongController',
            'Music\Controller\SongRest' => 'Music\Controller\SongRestController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'music' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/admin/music',
                    'defaults' => array(
                        'controller' => 'Music\Controller\Music',
                        'action'     => 'index',
                    ),
                ),
            ),
            'genre' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/genres[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Music\Controller\Genre',
                        'action'     => 'index',
                    ),
                ),
            ),
            'artist' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/artist[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Music\Controller\Artist',
                        'action'     => 'index',
                    ),
                ),
            ),
            'album' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/album[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Music\Controller\Album',
                        'action'     => 'index',
                    ),
                ),
            ),
            'song' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/song[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Music\Controller\Song',
                        'action'     => 'index',
                    ),
                ),
            ),
            'songservice' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/songservice[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Music\Controller\SongRest',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'music' => __DIR__ . '/../view',
        ),
    ),

    // Doctrine config
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'),
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver',
                ),
            ),
        ),
    ),
);