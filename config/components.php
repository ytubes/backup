<?php
return [
    'components' => [
        // list of component configurations
        'dbManager' => [
            'class' => 'ytubes\backup\components\DbManager',
        ],
        'fileManager' => [
            'class' => 'ytubes\backup\components\FileManager',
        ],
    ],
];