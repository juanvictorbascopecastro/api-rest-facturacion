<?php

use yii\db\Migration;

/**
 * Class m240607_005908_token_online
 */
class m240607_005908_token_online extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%token_online}}', [
            'id' => $this->primaryKey(),
            'idUser' => $this->string(200)->notNull(),
            'token' => $this->text()->notNull(),
            'recycleBin' => $this->boolean()->defaultValue(false),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('token_online');
        return false;
    }
}
