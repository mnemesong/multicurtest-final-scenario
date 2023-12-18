<?php

namespace app\models\activeRecords;

use app\models\traits\SaveStrictlyARTrait;
use Pantagruel74\MulticurtestCurrencyManager\records\CurrencyConvMultiplierRecInterface;
use yii\db\ActiveRecord;

/**
 * @property string $uuid
 * @property string $fromCurId
 * @property string $toCurId
 * @property int $timestamp
 * @property float $multiplier
 */
class CurrencyConvMultiplierLogAR extends ActiveRecord implements
    CurrencyConvMultiplierRecInterface
{
    use SaveStrictlyARTrait;

    public static function tableName(): string
    {
        return "{{%currency_conv_multipliers}}";
    }

    public function rules(): array
    {
        return [
            [["uuid", "fromCurId", "toCurId", "timestamp", "multiplier"],
                "required"],
            [["fromCurId", "toCurId"], "string", "length" => [2, 8]],
            ["timestamp", "number", "onlyInteger", "min" => 0],
            ["multiplier", "number"]
        ];
    }

    public function getFromCurId(): string
    {
        return $this->fromCurId;
    }

    public function getToCurId(): string
    {
        return $this->toCurId;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getMultiplier(): float
    {
        return $this->multiplier;
    }
}