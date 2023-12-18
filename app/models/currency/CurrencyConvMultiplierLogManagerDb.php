<?php

namespace app\models\currency;

use app\models\activeRecords\CurrencyConvMultiplierLogAR;
use Pantagruel74\MulticurtestCurrencyManager\managers\CurrencyConvMultiplierMangerInterface;
use Pantagruel74\MulticurtestCurrencyManager\records\CurrencyConvMultiplierRecInterface;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class CurrencyConvMultiplierLogManagerDb implements
    CurrencyConvMultiplierMangerInterface
{
    public function getMultipliersBetween(string $cur1, string $cur2): array
    {
        return CurrencyConvMultiplierLogAR::find()
            ->where(["and", ["fromCurId" => $cur1], ["toCurId" => $cur2]])
            ->orWhere(["and", ["fromCurId" => $cur2], ["toCurId" => $cur1]])
            ->all();
    }

    public function createNewMultiplier(
        string $curFrom,
        string $curTo,
        float $multi
    ): CurrencyConvMultiplierRecInterface {
        return new CurrencyConvMultiplierLogAR([
            "uuid" => Uuid::uuid4()->toString(),
            "fromCurId" => $curFrom,
            "toCurId" => $curTo,
            "timestamp" => (new \DateTime("now"))->getTimestamp(),
            "multiplier" => $multi
        ]);
    }

    public function saveNewMany(array $curMultipliers): void
    {
        Assert::allIsAOf($curMultipliers, CurrencyConvMultiplierLogAR::class);
        /* @var CurrencyConvMultiplierLogAR[] $curMultipliers */
        $t = \Yii::$app->db->beginTransaction();
        try {
            foreach ($curMultipliers as $cm) {
                $cm->saveStrictly();
            }
            $t->commit();
        } catch (\Throwable $e) {
            $t->rollBack();
            throw $e;
        }
    }
}