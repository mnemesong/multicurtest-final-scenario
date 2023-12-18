<?php

use yii\db\Migration;
use app\models\activeRecords\CurrencyInBankAccountAR;

/**
 * Handles the creation of table `{{%currency_in_bank_account}}`.
 */
class m231218_024658_create_currency_in_bank_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(CurrencyInBankAccountAR::tableName(), [
            'curId' => $this->string(16)->notNull(),
            'bankAccUuid' => $this->string(128)->notNull()
        ]);
        $this->addPrimaryKey("currency_in_bank_acc_pk",
            CurrencyInBankAccountAR::tableName(), ['curId', 'bankAccUuid']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(CurrencyInBankAccountAR::tableName());
    }
}
