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
    'currencyManager' => fn() => new \Pantagruel74\MulticurtestCurrencyManager\CurrencyManager(
        new \app\models\singletons\currency\CurrencyDefManagerDb(),
        new \app\models\singletons\currency\CurrencyConvMultiplierLogManagerDb()
    ),
    'accountAdministrationsService' => fn() =>
        new \Pantagruel74\MulticurtestAccountAdministrationsService\AccountAdministrationsService(
            \Yii::$app->currencyManager,
            new \app\models\singletons\accountAdministrations\BankAccountManagerDb()
        ),
    'privateOperationsService' => fn() =>
        new \Pantagruel74\MulticurtestPrivateOperationsService\PrivateOperationsService(
            new \app\models\singletons\privateOperations\BankAccountManagerDb(),
            new \app\models\singletons\privateOperations\CurrencySummaryManagerDb(),
            \Yii::$app->currencyManager,
            new \app\models\singletons\privateOperations\CurrencyOperationInAccountManagerDb()
        ),
    'bankManagementService' => fn() =>
        new \Pantagruel74\MulticurtestBankManagementService\BankManagementService(
            new \app\models\singletons\bankManagement\BankAccountManagerDb(),
            \Yii::$app->currencyManager,
            new \app\models\singletons\bankManagement\BankAccountBalanceManagerDb()
        ),
];