<?php
$ds = DIRECTORY_SEPARATOR;
return array_merge(
    require_once __DIR__ . $ds . 'base.php',
    [
        'request' => [
            'baseUrl' => '',
            'cookieValidationKey' => '00i-Ji17czC6gOrzQX3bxFOcoi-G0ays',
            'parsers' => [
                'application/json' => [
                    'class' => \yii\web\JsonParser::class,
                    'asArray' => true,
                ],
            ],
        ],
        'user' => [
            'identityClass' => \app\models\user\User::class,
            'enableAutoLogin' => true,
        ],
        'urlManager' => [
            'baseUrl' => '/',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ]
);