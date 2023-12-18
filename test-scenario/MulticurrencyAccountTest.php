<?php

namespace Pantagruel74\MulticurtestFinalScenarioTest;

use app\models\activeRecords\BankAccountAR;
use app\models\activeRecords\CurrencyConvMultiplierLogAR;
use app\models\activeRecords\CurrencyDefAR;
use app\models\activeRecords\CurrencyInBankAccountAR;
use app\models\activeRecords\CurrencyOperationInAccountAR;
use app\models\activeRecords\CurrencySummaryInAccountAR;
use app\models\activeRecords\DescriptionInCurrencyOperationInAccountAR;
use Pantagruel74\MulticurtestBankManagementService\values\CurrencyConversionMultiplierVal;
use Pantagruel74\MulticurtestCurrencyManager\value\AmountInCurrencyVal;
use Pantagruel74\Yii2TestAppTestHelpers\AbstractBaseTest;

class MulticurrencyAccountTest extends AbstractBaseTest
{
    private string $accId;

    /**
     * @return array
     */
    protected function getConfig(): array
    {
        $ds = DIRECTORY_SEPARATOR;
        $config = include dirname(__DIR__) . $ds . 'config' . $ds . 'base.php';
        return $config;
    }

    /**
     * Main test scenario!
     * @return void
     */
    protected function testScenario(): void
    {
        $this->clearDb();

        echo "\n\n0.";
        $this->bankInit();

        echo "\n\n1.";
        $accountId = $this->customerCreatesAccount("RUB");
        $this->customerAssertAccountMainCurrency($accountId, "RUB");
        $this->customerAddsCurrenciesToAccount($accountId, ["EUR", "USD"]);
        $this->customerAssertsListOfCurrencies($accountId, ["EUR", "RUB", "USD"]);
        $this->customerReplenishment($accountId, 1000, "RUB");
        $this->customerReplenishment($accountId, 50, "EUR");
        $this->customerReplenishment($accountId, 50, "USD");

        echo "\n\n2.";
        $this->customerAssertTotalBalance($accountId, 8500, "RUB");
        $this->customerAssertCurrencyBalance($accountId, 1000, "RUB");
        $this->customerAssertCurrencyBalance($accountId, 50, "USD");
        $this->customerAssertCurrencyBalance($accountId, 50, "EUR");

        echo "\n\n3.";
        $this->customerReplenishment($accountId, 1000, "RUB"); //2000 RUB
        $this->customerReplenishment($accountId, 50, "EUR"); //100 EUR
        $this->customerCashing($accountId, 10, "USD"); //40 USD

        echo "\n\n4.";
        $this->bankSetsCurrencyRatio("EUR", "RUB", 150);
        $this->bankSetsCurrencyRatio("USD", "RUB", 100);

        echo "\n\n5.";
        $this->customerAssertTotalBalance($accountId, 21000, "RUB");
        //2000 + (150 * 100) + (100 * 40) = 21000

        echo "\n\n6.";
        $this->customerChangeMainCurrency($accountId, "EUR");
        $this->customerAssertTotalBalance($accountId, 153.33, "EUR");
        //100 + (2000 / 150) + (40 / 1) = 153.33

        echo "\n\n7.";
        $this->customerConverts($accountId, "RUB", 1000, "EUR");
        $this->customerAssertCurrencyBalance($accountId, 106.66, "EUR");

        echo "\n\n8.";
        $this->bankSetsCurrencyRatio("EUR", "RUB", 120);

        echo "\n\n9.";
        $this->customerAssertCurrencyBalance($accountId, 106.66, "EUR");
        //EUR: 106.66   RUB: 1000   USD: 40

        echo "\n\n10.";
        $this->bankSwitchsOffCurrency("USD", "RUB");
        $this->bankSwitchsOffCurrency("EUR", "RUB");
        $this->bankAssertListOfExistingCurrencies(["RUB"]);
        $this->customerAssertTotalBalance($accountId, 17799.2, "RUB");
        //1000 + (40 * 100 = 4000) + (106.66 * 120 = 12799.2) = 17799.2
    }

    /**
     * @return void
     */
    private function clearDb(): void
    {
        echo "\nОчистка БД";
        BankAccountAR::deleteAll();
        CurrencyConvMultiplierLogAR::deleteAll();
        CurrencyDefAR::deleteAll();
        CurrencyInBankAccountAR::deleteAll();
        CurrencyOperationInAccountAR::deleteAll();
        CurrencySummaryInAccountAR::deleteAll();
        DescriptionInCurrencyOperationInAccountAR::deleteAll();
    }

    /**
     * @return void
     */
    private function bankInit(): void
    {
        \Yii::$app->bankManagementService
            ->createNewCurrency("RUB", [], 2);
        \Yii::$app->bankManagementService->createNewCurrency("EUR", [
                new CurrencyConversionMultiplierVal("RUB", 80)
            ], 2);
        \Yii::$app->bankManagementService->createNewCurrency("USD", [
                new CurrencyConversionMultiplierVal("EUR", 1),
                new CurrencyConversionMultiplierVal("RUB", 70)
            ], 2);
        echo "\n\n0.";
        echo "\nИнициализирован банк с валютами: " . implode(
                ", ",
                \Yii::$app->currencyManager->getAllCurrenciesExists());
        $this->bankAssertListOfExistingCurrencies(["RUB", "EUR", "USD"]);
    }

    /**
     * @param string $curId
     * @return string
     */
    private function customerCreatesAccount(string $curId): string
    {
        \Yii::$app->accountAdministrationsService
            ->createAccountWithOneCurrency("RUB");
        $accId = BankAccountAR::find()->all()[0]->uuid;
        echo "\nКлимент создал валютный счет в " . $curId
            . " с ID:" . $accId;
        $this->assertNotEmpty($accId);
        return $accId;
    }

    /**
     * @param string $accId
     * @param string $curId
     * @return void
     */
    private function customerAssertAccountMainCurrency(
        string $accId,
        string $curId
    ): void {
        $mainCurrency = \Yii::$app->accountAdministrationsService
            ->getMainCurrencyOfAccount($accId);
        echo "\nКлиент проверяет основную валюту счета " . $accId . ", ожидает"
            . " увидеть " . $curId . " и видит " . $mainCurrency;
        $this->assertEquals($curId, $mainCurrency);
    }

    /**
     * @param string $accId
     * @param array $curs
     * @return void
     */
    private function customerAddsCurrenciesToAccount(
        string $accId,
        array $curs
    ): void {
        \Yii::$app->accountAdministrationsService
            ->addCurrenciesToAccount($accId, $curs);
        echo "\nКлиент добавляет новые валюты к аккаунту: "
            . implode(", ", $curs);
    }

    /**
     * @param string $accId
     * @param array $expectCurs
     * @return void
     */
    private function customerAssertsListOfCurrencies(
        string $accId,
        array $expectCurs
    ): void {
        $listOfCurs = \Yii::$app->accountAdministrationsService
            ->getListOfCurrenciesInAccount($accId);
        echo "\nКлиент проверяет список валют аккаунта, ожилая увидеть: "
            . implode(", ", $expectCurs) . ", и видит: "
            . implode(", ", $listOfCurs);
        $this->assertEquals($expectCurs, $listOfCurs);
    }

    /**
     * @param string $accId
     * @param float $val
     * @param string $currency
     * @return void
     */
    private function customerReplenishment(
        string $accId,
        float $val,
        string $currency
    ): void {
        \Yii::$app->privateOperationsService->replenishmentOfBalance(
            $accId,
            \Yii::$app->currencyManager->numberToCurrencyAmount($currency, $val)
        );
        echo "\nКлиент пополнил баланс на " . $val . " " . $currency;
    }

    /**
     * @param string $accId
     * @param float $val
     * @param string $currency
     * @return void
     */
    private function customerCashing(
        string $accId,
        float $val,
        string $currency
    ): void {
        \Yii::$app->privateOperationsService->cashAmount(
            $accId,
            \Yii::$app->currencyManager->numberToCurrencyAmount($currency, $val)
        );
        echo "\nКлиент обналичил " . $val . " " . $currency;
    }

    /**
     * @param string $accId
     * @param float $expectVal
     * @param string $curId
     * @return void
     */
    private function customerAssertCurrencyBalance(
        string $accId,
        float $expectVal,
        string $curId
    ): void {
        $amount = \Yii::$app->privateOperationsService
            ->getConfirmedBalanceInCurrencyAccount($accId, $curId);
        echo "\nКлиент проверяет баланс в валюте " . $curId . ", ожидает увидеть:"
            . $expectVal . " " . $curId . ". Видит: " . $amount->toNumber() . " "
            . $amount->getCurId() . ".";
        $this->assertEquals($expectVal, $amount->toNumber());
        $this->assertEquals($curId, $amount->getCurId());
    }

    /**
     * @param string $accId
     * @param float $expectVal
     * @param string $curId
     * @return void
     */
    private function customerAssertTotalBalance(
        string $accId,
        float $expectVal,
        string $curId
    ): void {
        $amount = \Yii::$app->privateOperationsService
            ->getConfirmedTotalBalanceInAccount($accId);
        echo "\nКлиент проверяет общий баланс (стоимость) аккаунта, ожидает увидеть:"
            . $expectVal . " " . $curId . ". Видит: " . $amount->toNumber() . " "
            . $amount->getCurId() . ".";
        $this->assertEquals($expectVal, $amount->toNumber());
        $this->assertEquals($curId, $amount->getCurId());
    }

    /**
     * @param string $accId
     * @param string $curId
     * @return void
     */
    private function customerChangeMainCurrency(
        string $accId,
        string $curId
    ): void {
        \Yii::$app->accountAdministrationsService
            ->setMainCurrencyToAccount($accId, $curId);
        echo "Клиент меняет основную валюту аккаунта на " . $curId;
    }

    /**
     * @param string $accId
     * @param string $fromCur
     * @param float $val
     * @param string $toCur
     * @return void
     */
    private function customerConverts(
        string $accId,
        string $fromCur,
        float $val,
        string $toCur
    ): void {
        \Yii::$app->privateOperationsService->convertAmountToOtherCurrency(
            $accId,
            \Yii::$app->currencyManager->numberToCurrencyAmount($fromCur, $val),
            $toCur
        );
        echo "\nКлиент решает сконвертировать " . $val . " " . $fromCur . " в "
            . $toCur . ".";
    }

    /**
     * @param string $from
     * @param string $to
     * @param float $ratio
     * @return void
     */
    private function bankSetsCurrencyRatio(
        string $from,
        string $to,
        float $ratio
    ): void {
        \Yii::$app->bankManagementService
            ->changeConversionMultiplierForCurrency($from,
                new CurrencyConversionMultiplierVal($to, $ratio));
        echo "\nБанк устанавливает курс обмена: " . $from . "/" . $to . " = "
            . $ratio;
    }

    /**
     * @param string $switchedOffCur
     * @param string $defaultCur
     * @return void
     */
    private function bankSwitchsOffCurrency(
        string $switchedOffCur,
        string $defaultCur
    ): void {
        \Yii::$app->bankManagementService
            ->switchOffCurrency($switchedOffCur, $defaultCur);
        echo "\nБанк отказывется от валюты " . $switchedOffCur
            . ", конвертирует балансы всех клиентов в " . $defaultCur;
    }

    /**
     * @param array $curs
     * @return void
     */
    private function bankAssertListOfExistingCurrencies(array  $curs): void
    {
        echo "\nБанк проверяет что список всех доступных валют равен: "
            . implode(", ", $curs);
        $this->assertEquals(
            $curs,
            \Yii::$app->currencyManager->getAllCurrenciesExists()
        );
    }

    /**
     * @param AmountInCurrencyVal $a
     * @return string
     */
    private function printAmount(AmountInCurrencyVal $a): string
    {
        return $a->toNumber() . " " . $a->getCurId();
    }

    /**
     * @param int $s
     * @return void
     */
    private function printStepHeader(int $s): void
    {
        echo "\n\n" . $s . ".";
    }
}