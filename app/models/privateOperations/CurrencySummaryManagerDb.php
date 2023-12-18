<?php

namespace app\models\privateOperations;

use app\models\activeRecords\CurrencySummaryInAccountAR;
use Pantagruel74\MulticurtestPrivateOperationsService\managers\CurrencySummaryManagerInterface;
use Pantagruel74\MulticurtestPrivateOperationsService\records\CurrencySummaryInAccountRecInterface;
use yii\db\ActiveQuery;

class CurrencySummaryManagerDb implements CurrencySummaryManagerInterface
{
    private function getQuery(): ActiveQuery
    {
        return CurrencySummaryInAccountAR::find();
    }

    public function getLastSummaryForAccount(
        string $accId,
        string $curId
    ): ?CurrencySummaryInAccountRecInterface {
        $rec = $this->getQuery()
            ->andWhere(['curId' => $curId])
            ->andWhere(['accountUuid' => $accId])
            ->one();
        $rec = empty($rec) ? null : $rec;
        /* @var CurrencySummaryInAccountAR|null $rec */
        return $rec;
    }
}