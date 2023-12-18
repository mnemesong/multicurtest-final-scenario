<?php

use yii\db\Migration;
use app\models\activeRecords\CurrencyConvMultiplierLogAR;

/**
 * Handles the creation of table `{{%currency_conv_multipliers}}`.
 */
class m231218_023927_create_currency_conv_multipliers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(CurrencyConvMultiplierLogAR::tableName(), [
            'uuid' => $this->string(128)->notNull()->unique(),
            'fromCurId' => $this->string(16)->notNull(),
            'toCurId' => $this->string(16)->notNull(),
            'timestamp' => $this->bigInteger()->notNull(),
            'multiplier' => $this->float()->notNull(),
        ]);
        $this->addPrimaryKey(
            "currency_conv_multipliers_pk",
            CurrencyConvMultiplierLogAR::tableName(),
            ['uuid']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(CurrencyConvMultiplierLogAR::tableName());
    }
}
