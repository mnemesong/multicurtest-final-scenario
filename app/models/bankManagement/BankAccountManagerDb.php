<?php

namespace app\models\bankManagement;

use app\models\activeRecords\BankAccountAR;
use Pantagruel74\MulticurtestBankManagementService\managers\BankAccountMangerInterface;
use Pantagruel74\MulticurtestBankManagementService\records\BankAccountRecInterface;
use Webmozart\Assert\Assert;
use yii\db\ActiveQuery;

class BankAccountManagerDb implements BankAccountMangerInterface
{
    /**
     * @return ActiveQuery
     */
    private function getQuery(): ActiveQuery
    {
        return BankAccountAR::find()
            ->joinWith('currenciesInAccount');
    }

    /**
     * @return BankAccountAR[]
     */
    public function getAllAccounts(): array
    {
        return $this->getQuery()
            ->all();
    }

    /**
     * @param BankAccountAR[] $accs
     * @return void
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function saveAccounts(array $accs): void
    {
        Assert::allIsAOf($accs, BankAccountAR::class);
        $t = \Yii::$app->db->beginTransaction();
        try {
            foreach ($accs as $acc) {
                $acc->saveWithCurrency(false);
            }
            $t->commit();
        } catch (\Throwable $e) {
            $t->rollBack();
            throw $e;
        }
    }
}