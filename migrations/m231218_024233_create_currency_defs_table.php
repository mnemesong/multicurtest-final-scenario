<?php

use yii\db\Migration;
use app\models\activeRecords\CurrencyDefAR;

/**
 * Handles the creation of table `{{%currency_defs}}`.
 */
class m231218_024233_create_currency_defs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(CurrencyDefAR::tableName(), [
            'curId' => $this->string(16)->notNull()->unique(),
            'dotPosition' => $this->tinyInteger()->unsigned()->defaultValue(0),
            'available' => $this->boolean()->defaultValue(false),
        ]);
        $this->addPrimaryKey('currency_defs_pk', CurrencyDefAR::tableName(),
            ['curId']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(CurrencyDefAR::tableName());
    }
}
