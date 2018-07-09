<?php

/**
 * Модель для импорта данных из базы fias в mysql базу
 */

namespace solbianca\fias\console\models;

use solbianca\fias\console\base\XmlReader;
use solbianca\fias\console\events\ImportEvent;
use solbianca\fias\models\FiasAddressObject;
use solbianca\fias\models\FiasAddressObjectLevel;
use solbianca\fias\models\FiasHouse;
use yii\db\Exception;
use yii\helpers\Console;

class ImportModel extends BaseModel
{

    const EVENT_BEFORE_IMPORT = 'beforeImport';

    const EVENT_AFTER_IMPORT = 'afterImport';

    /**
     * @throws \Exception
     */
    public function run()
    {
        return $this->import();
    }

    /**
     * Import fias data in base
     *
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function import()
    {
        try {
            $this->db->createCommand('SET foreign_key_checks = 0;')->execute();

            $this->trigger(self::EVENT_BEFORE_IMPORT, new ImportEvent($this->db));

            $this->dropIndexes();

            $this->importAddressObjectLevel();

            $this->importAddressObject();

            $this->importHouse();

            $this->addIndexes();

            $this->trigger(self::EVENT_AFTER_IMPORT, new ImportEvent($this->db));

            $this->saveLog();

            $this->db->createCommand('SET foreign_key_checks = 1;')->execute();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Import fias address object
     */
    private function importAddressObject()
    {
        Console::output('Импорт адресов обектов');
        FiasAddressObject::import(new XmlReader(
            $this->directory->getAddressObjectFile(),
            FiasAddressObject::XML_OBJECT_KEY,
            array_keys(FiasAddressObject::getXmlAttributes()),
            FiasAddressObject::getXmlFilters()
        ));
    }

    /**
     * Import fias house
     */
    private function importHouse()
    {
        Console::output('Импорт домов');
        FiasHouse::import(new XmlReader(
            $this->directory->getHouseFile(),
            FiasHouse::XML_OBJECT_KEY,
            array_keys(FiasHouse::getXmlAttributes()),
            FiasHouse::getXmlFilters()
        ));
    }

    /**
     * Import fias address object levels
     */
    private function importAddressObjectLevel()
    {
        Console::output('Импорт типов адресных объектов (условные сокращения и уровни подчинения)');
        FiasAddressObjectLevel::import(
            new XmlReader(
                $this->directory->getAddressObjectLevelFile(),
                FiasAddressObjectLevel::XML_OBJECT_KEY,
                array_keys(FiasAddressObjectLevel::getXmlAttributes()),
                FiasAddressObjectLevel::getXmlFilters()
            )
        );
    }

    /**
     * Get fias base version
     *
     * @param $directory \solbianca\fias\console\base\Directory
     *
     * @return string
     */
    protected function getVersion($directory)
    {
        return $this->fileInfo->getVersionId();
    }

    /**
     * @throws \yii\db\Exception
     *
     * Сбрасываем индексы для таблиц данных фиас
     */
    protected function dropIndexes()
    {
        Console::output('Сбрасываем индексы и ключи.');

        Console::output('Сбрасываем внешние ключи.');
        $this->dropForeignKeyIfExists('houses_parent_id_fkey', '{{%fias_house}}');
        $this->dropForeignKeyIfExists('address_object_parent_id_fkey', '{{%fias_address_object}}');
        $this->dropForeignKeyIfExists('fk_region_code_ref_fias_region', '{{%fias_address_object}}');

        Console::output('Сбрасываем индексы.');
        $this->dropIndexIfExists('region_code', '{{%fias_address_object}}');
        $this->dropIndexIfExists('house_address_id_fkey_idx', '{{%fias_house}}');
        $this->dropIndexIfExists('address_object_parent_id_fkey_idx', '{{%fias_address_object}}');
        $this->dropIndexIfExists('address_object_title_lower_idx', '{{%fias_address_object}}');

        Console::output('Сбрасываем первичные ключи.');
        $this->dropPrimaryKeyIfExists('pk', '{{%fias_house}}');
        $this->dropPrimaryKeyIfExists('pk', '{{%fias_address_object}}');
        $this->dropPrimaryKeyIfExists('pk', '{{%fias_address_object_level}}');

    }

    /**
     * @throws \yii\db\Exception
     * Устанавливаем индексы для таблиц данных фиас
     */
    protected function addIndexes()
    {

        $db = $this->db;

        Console::output('Добавляем к данным индексы и ключи.');

        Console::output('Создаем первичные ключи.');
        $db->createCommand()->addPrimaryKey('pk', '{{%fias_house}}', 'id')->execute();
        $db->createCommand()->addPrimaryKey('pk', '{{%fias_address_object}}', 'id')->execute();
        $db->createCommand()->addPrimaryKey('pk', '{{%fias_address_object_level}}',
            ['title', 'code'])->execute();

        Console::output('Добавляем индексы.');
        $db->createCommand()->createIndex('region_code', '{{%fias_address_object}}',
            'region_code')->execute();
        $db->createCommand()->createIndex('house_address_id_fkey_idx', '{{%fias_house}}',
            'address_id')->execute();
        $db->createCommand()->createIndex('address_object_parent_id_fkey_idx',
            '{{%fias_address_object}}',
            'parent_id')->execute();
        $db->createCommand()->createIndex('address_object_title_lower_idx', '{{%fias_address_object}}',
            'title')->execute();

        Console::output('Добавляем внешние ключи.');
        $db->createCommand()->addForeignKey('houses_parent_id_fkey', '{{%fias_house}}', 'address_id',
            '{{%fias_address_object}}',
            'address_id', 'CASCADE', 'CASCADE')->execute();
        $db->createCommand()->addForeignKey('address_object_parent_id_fkey', '{{%fias_address_object}}',
            'parent_id',
            '{{%fias_address_object}}', 'address_id', 'CASCADE', 'CASCADE')->execute();
        $db->createCommand()->addForeignKey('fk_region_code_ref_fias_region', '{{%fias_address_object}}',
            'region_code',
            '{{%fias_region}}', 'code', 'NO ACTION', 'NO ACTION')->execute();
    }

    /**
     * @param $name
     * @param $table
     */
    private function dropForeignKeyIfExists($name, $table)
    {
        try {
            $this->db->createCommand()->dropForeignKey($name, $table)->execute();
        } catch (Exception $ignored) {

        }
    }


    /**
     * @param $name
     * @param $table
     */
    private function dropIndexIfExists($name, $table)
    {
        try {
            $this->db->createCommand()->dropIndex($name, $table)->execute();
        } catch (Exception $ignored) {

        }
    }

    /**
     * @param $name
     * @param $table
     *
     * @throws \yii\db\Exception
     */
    private function dropPrimaryKeyIfExists($name, $table)
    {
        try {
            $this->db->createCommand()->dropPrimaryKey($name, $table)->execute();
        } catch (Exception $ignored) {

        }
    }

}