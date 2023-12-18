<?php

namespace app\models\activeRecords;

use app\models\traits\SaveStrictlyARTrait;
use Pantagruel74\MulticurtestCurrencyManager\value\AmountInCurrencyVal;
use Pantagruel74\MulticurtestPrivateOperationsService\records\CurrencyOperationInAccountRequestRecInterface;
use Pantagruel74\MulticurtestPrivateOperationsService\values\AmountInCurrencyValInterface;
use Webmozart\Assert\Assert;
use yii\db\ActiveRecord;

/**
 * @property string $uuid
 * @property int $amountDecades
 * @property int $amountDotPosition
 * @property string $curId
 * @property string $accId
 * @property int $timestamp
 * @property string $opType
 * @property bool $confirmed
 * @property bool $declined
 */
class CurrencyOperationInAccountAR extends ActiveRecord implements
    CurrencyOperationInAccountRequestRecInterface
{
    use SaveStrictlyARTrait;

    const OP_TYPE_REPLENISHMENT = "REPL";
    const OP_TYPE_CASHING = "CASH";
    const OP_TYPE_CUSTOMER_CONV_WRITE_OFF = "CCWO";
    const OP_TYPE_CUSTOMER_CONV_WRITE_IN = "CCWI";
    const OP_TYPE_BANK_CONV_OPERATION = "BCO";

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return "{{%currency_operations_in_account}}";
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['uuid', 'curId', 'accId', 'opType'], 'required'],
            ['opType', 'string', 'length' => [2, 8]],
            [['amountDotPosition', 'timestamp'],
                'number', "integerOnly" => true, 'min' => 0],
            ['amountDecades', 'number', 'integerOnly' => true]
        ];
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->uuid;
    }

    /**
     * @return AmountInCurrencyValInterface
     */
    public function getAmount(): AmountInCurrencyValInterface
    {
        return new AmountInCurrencyVal(
            $this->amountDecades,
            $this->amountDotPosition,
            $this->curId
        );
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->confirmed == true;
    }

    /**
     * @return bool
     */
    public function isDeclined(): bool
    {
        return $this->declined == true;
    }

    /**
     * @return $this
     */
    public function asDeclined(): self
    {
        Assert::false($this->confirmed === true,
            "Can't decline confirmed operation " . $this->uuid);
        $c = clone $this;
        $c->declined = true;
        return $c;
    }
}