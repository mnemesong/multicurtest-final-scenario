<?php

namespace app\models\activeRecords;

use app\models\traits\SaveStrictlyARTrait;
use Pantagruel74\MulticurtestCurrencyManager\records\CurrencyDefRecInterface;
use yii\db\ActiveRecord;

/**
 * @property string $curId
 * @property int $dotPosition
 * @property bool $available
 */
class CurrencyDefRecordAR extends ActiveRecord implements CurrencyDefRecInterface
{
    use SaveStrictlyARTrait;

    public static function tableName(): string
    {
        return "{{%currency_defs}}";
    }

    public function rules(): array
    {
        return [
            ["curId", "required"],
            ["curId", "string", "length" => [2, 8]],
            ["dotPosition", "number", "integerOnly" => true, "min" => 0, "max" => 8],
        ];
    }

    public function getCurId(): string
    {
        return $this->curId;
    }

    public function getDotPosition(): int
    {
        return $this->dotPosition;
    }

    public function isAvailable(): bool
    {
        return $this->available == true;
    }

    public function asUnavailable(): CurrencyDefRecInterface
    {
        $c = clone $this;
        $c->available = false;
        return $c;
    }
}