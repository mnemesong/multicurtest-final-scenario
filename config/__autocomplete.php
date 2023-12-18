<?php

/**
 * This class only exists here for IDE (PHPStorm/Netbeans/...) autocompletion.
 * This file is never included anywhere.
 * Adjust this file to match classes configured in your application config, to enable IDE autocompletion for custom components.
 * Example: A property phpdoc can be added in `__Application` class as `@property \vendor\package\Rollbar|__Rollbar $rollbar` and adding a class in this file
 * ```php
 * // @property of \vendor\package\Rollbar goes here
 * class __Rollbar {
 * }
 * ```
 */
class Yii {
    /**
     * @var \yii\web\Application|\yii\console\Application|__Application
     */
    public static $app;
}

/**
 * @property yii\rbac\DbManager $authManager 
 * @property \yii\web\User|__WebUser $user
 * @property \Pantagruel74\MulticurtestAccountAdministrationsService\AccountAdministrationsService $accountAdministrationsService
 * @property \Pantagruel74\MulticurtestPrivateOperationsService\PrivateOperationsService $privateOperationsService
 * @property \Pantagruel74\MulticurtestBankManagementService\BankManagementService $bankManagementService
 * @property \Pantagruel74\MulticurtestCurrencyManager\CurrencyManager $currencyManager
 */
class __Application {
}

/**
 * @property app\models\User $identity
 */
class __WebUser {
}
