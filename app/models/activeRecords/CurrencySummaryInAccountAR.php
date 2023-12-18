<?php

namespace app\models\activeRecords;

use app\models\traits\SaveStrictlyARTrait;
use Pantagruel74\MulticurtestCurrencyManager\value\AmountInCurrencyVal;
use Pantagruel74\MulticurtestPrivateOperationsService\records\CurrencySummaryInAccountRecInterface;
use Pantagruel74\MulticurtestPrivateOperationsService\values\AmountInCurrencyValInterface;
use yii\db\ActiveRecord;

/**
 * @property string $uuid
 * @property string $curId
 * @property string $accountUuid
 * @property int $amountDecades
 * @property int $amountDotPosition
 * @property int $timestamp
 */
class CurrencySummaryInAccountAR extends ActiveRecord implements
    CurrencySummaryInAccountRecInterface
{
    use SaveStrictlyARTrait;

    public static function tableName(): string
    {
        return "{{%currency_summaries_in_account}}";
    }

    public function rules(): array
    {
        return [
            [['uuid', 'curId', 'accountUuid', 'timestamp'], 'required'],
            [['timestamp', 'amountDotPosition'],
                'number', "integerOnly" => true, 'min' => 0],
            ['amountDecades', 'number', 'onlyInteger'],
        ];
    }

    public function getId(): string
    {
        return $this->uuid;
    }

    public function getCurId(): string
    {
        return $this->curId;
    }

    public function getAmount(): AmountInCurrencyValInterface
    {
        return new AmountInCurrencyVal(
            $this->amountDecades,
            $this->amountDotPosition,
            $this->curId
        );
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }
}