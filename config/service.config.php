<?php
return array(
    'factories' => array(
        'BowerModule\\Config\\Module' => 'BowerModule\\Config\\Factory',
    ),
    'invokables' => array(
        'BowerModule\\PathBuilder\\Service' => 'BowerModule\\PathBuilder\\Service',
        'BowerModule\\Bower\\Service' => 'BowerModule\\Bower\\Service',
    ),
);
