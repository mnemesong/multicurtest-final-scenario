<?php
$ds = DIRECTORY_SEPARATOR;
return [
    'cache' => [
        'class' => 'yii\caching\FileCache',
    ],
    'log' => [
        'targets' => [
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['error', 'warning'],
            ],
        ],
    ],
    'db' => include __DIR__ . $ds . 'db' . $ds . 'prod.php',
];