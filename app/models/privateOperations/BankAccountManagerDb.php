<?php

namespace app\models\privateOperations;

use app\models\activeRecords\BankAccountAR;
use Pantagruel74\MulticurtestPrivateOperationsService\managers\BankAccountManagerInterface;
use Pantagruel74\MulticurtestPrivateOperationsService\records\BankAccountRecInterface;
use Webmozart\Assert\Assert;
use yii\db\ActiveQuery;

class BankAccountManagerDb implements BankAccountManagerInterface
{
    private function query(): ActiveQuery
    {
        return BankAccountAR::find()
            ->joinWith('currenciesInAccount');
    }

    public function getAccount(string $id): BankAccountRecInterface
    {
        $rec = $this->query()
            ->andWhere(['uuid' => $id])
            ->one();
        Assert::notEmpty($rec, "Account " . $id . " had been not found");
        /* @var BankAccountAR $rec */
        return $rec;
    }
}