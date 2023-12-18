<?php

use yii\db\Migration;
use app\models\activeRecords\CurrencySummaryInAccountAR;

/**
 * Handles the creation of table `{{%currency_summary_in_account}}`.
 */
class m231218_030410_create_currency_summary_in_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(CurrencySummaryInAccountAR::tableName(), [
            'uuid' => $this->string(128)->unique()->notNull(),
            'curId' => $this->string(16)->notNull(),
            'accountUuid' => $this->string(128)->notNull(),
            'amountDecades' => $this->bigInteger()->defaultValue(0),
            'amountDotPosition' => $this->tinyInteger()->defaultValue(0),
            'timestamp' => $this->bigInteger(),
        ]);
        $this->addPrimaryKey("currency_summary_in_acc_pk",
            CurrencySummaryInAccountAR::tableName(), ["uuid"]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(CurrencySummaryInAccountAR::tableName());
    }
}
