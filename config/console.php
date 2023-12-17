<?php

$ds = DIRECTORY_SEPARATOR;
$config = array_merge(
    include "base.php",
    [
        'controllerNamespace' => 'app\controllers\console',
        'components' => include __DIR__ . $ds . 'components' . $ds . 'console.php',
        'params' => include __DIR__ . $ds . 'params' . $ds . 'console.php',
    ]
);

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
