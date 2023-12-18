<?php

use yii\db\Migration;
use app\models\activeRecords\DescriptionInCurrencyOperationInAccountAR;

/**
 * Handles the creation of table `{{%description_currency_operation_in_account}}`.
 */
class m231218_030708_create_description_currency_operation_in_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(DescriptionInCurrencyOperationInAccountAR::tableName(), [
            'curOpInAccUuid' => $this->string(128)->notNull()->notNull(),
            'desc' => $this->text(),
        ]);
        $this->addPrimaryKey("description_in_cur_op_in_acc_pk",
            DescriptionInCurrencyOperationInAccountAR::tableName(),
            ['curOpInAccUuid']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(DescriptionInCurrencyOperationInAccountAR::tableName());
    }
}
