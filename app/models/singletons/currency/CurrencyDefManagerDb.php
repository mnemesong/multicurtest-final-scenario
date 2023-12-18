<?php

namespace app\models\singletons\currency;

use app\models\activeRecords\CurrencyDefAR;
use Pantagruel74\MulticurtestCurrencyManager\managers\CurrencyDefManagerInterface;
use Pantagruel74\MulticurtestCurrencyManager\records\CurrencyDefRecInterface;
use Webmozart\Assert\Assert;

class CurrencyDefManagerDb implements CurrencyDefManagerInterface
{
    /**
     * @param string $curId
     * @return CurrencyDefRecInterface
     */
    public function getCurrency(string $curId): CurrencyDefRecInterface
    {
        $rec = CurrencyDefAR::find()
            ->andWhere(["curId" => $curId])
            ->one();
        Assert::notEmpty($rec, "Record " . $curId . " had been not found");
        /* @var CurrencyDefAR $rec */
        return $rec;
    }

    /**
     * @return CurrencyDefRecInterface[]
     */
    public function getAllAvailable(): array
    {
        return CurrencyDefAR::find()
            ->andWhere(["available" => 1])
            ->all();
    }

    public function create(
        string $curId,
        int $dotPosition
    ): CurrencyDefRecInterface {
        return new CurrencyDefAR([
            "curId" => $curId,
            "dotPosition" => $dotPosition,
            "available" => 1,
        ]);
    }

    public function save(CurrencyDefRecInterface $cur): void
    {
        Assert::isAOf($cur, CurrencyDefAR::class);
        /* @var CurrencyDefAR $cur */
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