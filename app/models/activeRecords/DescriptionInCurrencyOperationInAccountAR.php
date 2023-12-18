<?php

namespace app\models\activeRecords;

use app\models\traits\SaveStrictlyARTrait;
use yii\db\ActiveRecord;

/**
 * @property string $curOpInAccUuid
 * @property string $desc
 */
class DescriptionInCurrencyOperationInAccountAR extends ActiveRecord
{
    use SaveStrictlyARTrait;

    public static function tableName(): string
    {
        return '{{%descriptions_in_cur_operations_in_acc}}';
    }

    public function rules(): array
    {
        return [
            [['curOpInAccUuid', 'desc'], 'required']
        ];
    }
}