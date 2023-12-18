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

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return "{{%currency_conv_multipliers}}";
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [["uuid", "fromCurId", "toCurId", "timestamp", "multiplier"],
                "required"],
            [["fromCurId", "toCurId"], "string", "length" => [2, 8]],
            ["timestamp", "number", "integerOnly" => true, "min" => 0],
            ["multiplier", "number"]
        ];
    }

    /**
     * @return string
     */
    public function getFromCurId(): string
    {
        return $this->fromCurId;
    }

    /**
     * @return string
     */
    public function getToCurId(): string
    {
        return $this->toCurId;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return float
     */
    public function getMultiplier(): float
    {
        return $this->multiplier;
    }
}