<?php

use yii\db\Migration;
use app\models\activeRecords\BankAccountAR;

/**
 * Handles the creation of table `{{%bank_accounts}}`.
 */
class m231218_023633_create_bank_accounts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(BankAccountAR::tableName(), [
            'uuid' => $this->string(128)->unique()->notNull(),
            'mainCurId' => $this->string(16)->notNull(),
        ]);
        $this->addPrimaryKey(
            "bank_accounts_pk",
            BankAccountAR::tableName(),
            ['uuid']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(BankAccountAR::tableName());
    }
}
