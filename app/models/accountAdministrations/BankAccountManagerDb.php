<?php

namespace app\models\accountAdministrations;

use app\models\activeRecords\BankAccountAR;
use app\models\activeRecords\CurrencyInBankAccountAR;
use Pantagruel74\MulticurtestAccountAdministrationsService\managers\BankAccountMangerInterface;
use Pantagruel74\MulticurtestAccountAdministrationsService\records\BankAccountRecInterface;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;
use yii\db\ActiveQuery;

class BankAccountManagerDb implements BankAccountMangerInterface
{
    private function query(): ActiveQuery
    {
        return BankAccountAR::find()
            ->joinWith('currenciesInAccount')
            ->joinWith('summariesInAccount');
    }

    public function createAccount(string $mainCurrency): BankAccountRecInterface
    {
        Assert::true(
            \Yii::$app->currencyManager->isCurrenciesAvailable([$mainCurrency]),
            "Currency " . $mainCurrency . " unavailable"
        );
        return new BankAccountAR([
            'uuid' => Uuid::uuid4()->toString(),
            'mainCurId' => $mainCurrency,
        ]);
    }

    public function getAccount(string $accId): BankAccountRecInterface
    {
        $rec = $this->query()
            ->andWhere(['uuid' => $accId])
            ->one();
        Assert::notEmpty($rec, "Account " . $accId . " had been not found");
        /* @var BankAccountAR $rec */
        return $rec;
    }

    /**
     * @param BankAccountAR[] $accounts
     * @return void
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function saveBankAccounts(array $accounts): void
    {
        Assert::allIsAOf($accounts, BankAccountAR::class,
            "Invalid class of accounts");
        /* @var BankAccountAR[] $accounts */
        $t = \Yii::$app->db->beginTransaction();
        try {
            foreach ($accounts as $acc) {
                $acc->saveWithCurrency(false);
            }
            $t->commit();
        } catch (\Throwable $e) {
            $t->rollBack();
            throw $e;
        }
    }
}