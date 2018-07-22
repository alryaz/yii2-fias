<?php

use yii\db\Migration;

/**
 * Class m180722_012429_fias_address_parent_id
 */
class m180722_012429_fias_address_parent_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%fias_address_object}}', 'parent_id',
            $this->char(36)->null()->defaultValue(null)->comment('Идентификационный код родительского адресного объекта'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%fias_address_object}}', 'parent_id',
            $this->char(36)->notNull()->comment('Идентификационный код родительского адресного объекта'));
    }

}
