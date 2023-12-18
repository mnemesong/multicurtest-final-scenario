<?php

namespace app\models\singletons\accountAdministrations;

use app\models\activeRecords\BankAccountAR;
use Pantagruel74\MulticurtestAccountAdministrationsService\managers\BankAccountMangerInterface;
use Pantagruel74\MulticurtestAccountAdministrationsService\records\BankAccountRecInterface;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;
use yii\db\ActiveQuery;

class BankAccountManagerDb implements BankAccountMangerInterface
{
    /**
     * @return ActiveQuery
     */
    private function query(): ActiveQuery
    {
        return BankAccountAR::find()
            ->joinWith('currenciesInAccount')
            ->joinWith('summariesInAccount');
    }

    /**
     * @param string $mainCurrency
     * @return BankAccountRecInterface
     */
    public function createAccount(string $mainCurrency): BankAccountRecInterface
    {
        Assert::true(
            \Yii::$app->currencyManager->isCurrenciesAvailable([$mainCurrency]),
            "Currency " . $mainCurrency . " unavailable"
        );
        return new BankAccountAR([
            'uuid' => Uuid::uuid4()->toString(),
            'mainCurId' => $mainCurrency,
            "curIds" => [$mainCurrency],
        ]);
    }

    /**
     * @param string $accId
     * @return BankAccountRecInterface
     */
    public function getAccount(string $accId): BankAccountRecInterface
    {
        $rec = $this->query()
            ->andWhere([BankAccountAR::tableName().'.uuid' => $accId])
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