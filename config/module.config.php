<?php
return array(
    'console' => array(
        'router' => array(
            'routes' => array(
                'bower_prepare_packs' => array(
                    'options' => array(
                        'route' => 'bower prepare-packs',
                        'defaults' => array(
                            'controller' => 'bower\\console',
                            'action' => 'prepare-packs',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'bower\\console' => 'BowerModule\\Console',
        ),
    ),
    'bower' => array(
        'bower_folder' => array(
            'os' => 'bower_components',
        ),
        'pack_folder' => array(
            'os' => 'public/js',
            'web' => '/js',
        ),
        'debug_folder' => array(
            'os' => 'public/js/dev',
            'web' => '/js/dev',
        ),
        'debug_mode' => true,
    ),
);
