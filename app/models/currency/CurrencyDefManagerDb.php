<?php

namespace app\models\currency;

use app\models\activeRecords\CurrencyDefRecordAR;
use Pantagruel74\MulticurtestCurrencyManager\managers\CurrencyDefManagerInterface;
use Pantagruel74\MulticurtestCurrencyManager\records\CurrencyDefRecInterface;
use Webmozart\Assert\Assert;

class CurrencyDefManagerDb implements CurrencyDefManagerInterface
{
    public function getCurrency(string $curId): CurrencyDefRecInterface
    {
        $rec = CurrencyDefRecordAR::find()
            ->andWhere(["curId" => $curId])
            ->one();
        Assert::notEmpty($rec, "Record " . $curId . " had been not found");
        /* @var CurrencyDefRecordAR $rec */
        return $rec;
    }

    public function getAllAvailable(): array
    {
        return CurrencyDefRecordAR::find()
            ->andWhere(["available" => 1])
            ->all();
    }

    public function create(
        string $curId,
        int $dotPosition
    ): CurrencyDefRecInterface {
        return new CurrencyDefRecordAR([
            "curId" => $curId,
            "dotPosition" => $dotPosition,
            "available" => 1,
        ]);
    }

    public function save(CurrencyDefRecInterface $cur): void
    {
        Assert::isAOf($cur, CurrencyDefRecordAR::class);
        /* @var CurrencyDefRecordAR $cur */
        $t = \Yii::$app->db->beginTransaction();
        try {
            $cur->saveStrictly();
            $t->commit();
        } catch (\Throwable $e) {
            $t->rollBack();
            throw $e;
        }
    }
}