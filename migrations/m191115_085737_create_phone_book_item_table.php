<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%phone_book_item}}`.
 */
class m191115_085737_create_phone_book_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%phone_book_item}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(50)->notNull(),
            'last_name' => $this->string(50),
            'phone_number' => $this->string(17)->notNull(),
            'country_code' => $this->string(2),
            'timezone_name' => $this->string(50),
            'inserted_on' => $this->dateTime()->notNull(),
            'updated_on' => $this->dateTime()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%phone_book_item}}');
    }
}
