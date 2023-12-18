<?php

namespace app\models\singletons\currency;

use app\models\activeRecords\CurrencyConvMultiplierLogAR;
use Pantagruel74\MulticurtestBankManagementService\values\CurrencyConversionMultiplierVal;
use Pantagruel74\MulticurtestCurrencyManager\managers\CurrencyConvMultiplierMangerInterface;
use Pantagruel74\MulticurtestCurrencyManager\records\CurrencyConvMultiplierRecInterface;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class CurrencyConvMultiplierLogManagerDb implements
    CurrencyConvMultiplierMangerInterface
{
    /**
     * @param string $cur1
     * @param string $cur2
     * @return array|CurrencyConvMultiplierRecInterface[]
     */
    public function getMultipliersBetween(string $cur1, string $cur2): array
    {
        return CurrencyConvMultiplierLogAR::find()
            ->where(["and", ["fromCurId" => $cur1], ["toCurId" => $cur2]])
            ->orWhere(["and", ["fromCurId" => $cur2], ["toCurId" => $cur1]])
            ->all();
    }

    /**
     * @param string $curFrom
     * @param string $curTo
     * @param float $multi
     * @return CurrencyConvMultiplierRecInterface
     */
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

    /**
     * @param CurrencyConvMultiplierLogAR[] $curMultipliers
     * @return void
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
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