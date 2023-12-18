<?php
$ds = DIRECTORY_SEPARATOR;
$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => include __DIR__ . $ds . 'components' . $ds . 'base.php',
];

return $config;