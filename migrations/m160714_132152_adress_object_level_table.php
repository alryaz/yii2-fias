<?php

use yii\db\Migration;

/**
 * Class m160714_132152_adress_object_level_table
 */
class m160714_132152_adress_object_level_table extends Migration
{
    public function up()
    {
        $this->execute('SET foreign_key_checks = 0;');

        $this->addColumn('{{%fias_address_object_level}}', 'level',
            $this->integer()->comment('Уровень адресного объекта'));
        $this->addColumn('{{%fias_address_object_level}}', 'short_title',
            $this->string()->comment('Короткое обозначение')->after('title'));

        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->dropColumn('{{%fias_address_object_level}}', 'level');
        $this->dropColumn('{{%fias_address_object_level}}', 'short_title');
    }
}
