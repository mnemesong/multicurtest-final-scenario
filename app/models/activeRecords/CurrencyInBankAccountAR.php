<?php

namespace app\models\activeRecords;

use app\models\traits\SaveStrictlyARTrait;
use yii\db\ActiveRecord;

/**
 * @property string $curId
 * @property string $bankAccUuid
 */
class CurrencyInBankAccountAR extends ActiveRecord
{
    use SaveStrictlyARTrait;

    public static function tableName(): string
    {
        return "{{%currency_in_bank_account}}";
    }

    public function rules(): array
    {
        return [
            [["curId", "bankAccUuid"], "required"],
            ["curId", "string", "length" => [2, 8]],
        ];
    }
}