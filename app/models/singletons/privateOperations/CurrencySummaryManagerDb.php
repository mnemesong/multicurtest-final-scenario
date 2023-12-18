<?php

namespace app\models\singletons\privateOperations;

use app\models\activeRecords\CurrencySummaryInAccountAR;
use Pantagruel74\MulticurtestPrivateOperationsService\managers\CurrencySummaryManagerInterface;
use Pantagruel74\MulticurtestPrivateOperationsService\records\CurrencySummaryInAccountRecInterface;
use yii\db\ActiveQuery;

class CurrencySummaryManagerDb implements CurrencySummaryManagerInterface
{
    /**
     * @return ActiveQuery
     */
    private function getQuery(): ActiveQuery
    {
        return CurrencySummaryInAccountAR::find();
    }

    /**
     * @param string $accId
     * @param string $curId
     * @return CurrencySummaryInAccountRecInterface|null
     */
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