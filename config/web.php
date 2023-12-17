<?php
$ds = DIRECTORY_SEPARATOR;

$config = array_merge(
    include __DIR__ . $ds . 'base.php',
    [
        'controllerNamespace' => 'app\controllers\web',
        'components' => include __DIR__ . $ds . 'components' . $ds . 'web.php',
        'params' => include __DIR__ . $ds . 'params' . $ds . 'web.php',
    ]
);

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
