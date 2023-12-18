<?php

use yii\db\Migration;
use app\models\activeRecords\CurrencyOperationInAccountAR;

/**
 * Handles the creation of table `{{%currency_operations_in_account}}`.
 */
class m231218_024900_create_currency_operations_in_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(CurrencyOperationInAccountAR::tableName(), [
            'uuid' => $this->string(128)->unique()->notNull(),
            'amountDecades' => $this->bigInteger()->defaultValue(0),
            'amountDotPosition' => $this->tinyInteger()->defaultValue(0),
            'curId' => $this->string(16)->notNull(),
            'accId' => $this->string(128)->notNull(),
            'timestamp' => $this->bigInteger()->notNull(),
            'opType' => $this->string(8)->notNull(),
            'confirmed' => $this->boolean()->defaultValue(false),
            'declined' => $this->boolean()->defaultValue(false),
        ]);
        $this->addPrimaryKey("currency_operations_in_account",
            CurrencyOperationInAccountAR::tableName(), ['uuid']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(CurrencyOperationInAccountAR::tableName());
    }
}
