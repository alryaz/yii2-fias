<?php

/**
 * Модель для импорта данных из базы fias в mysql базу
 */

namespace solbianca\fias\console\components;

use solbianca\fias\console\base\Directory;
use solbianca\fias\console\base\SoapResultWrapper;
use solbianca\fias\console\base\XmlReader;
use solbianca\fias\console\events\ImportEvent;
use solbianca\fias\models\FiasAddressObject;
use solbianca\fias\models\FiasAddressObjectLevel;
use solbianca\fias\models\FiasHouse;
use yii\db\Exception;
use yii\helpers\Console;

class ImportFiasComponent extends FiasComponent
{

    const EVENT_BEFORE_IMPORT = 'beforeImport';

    const EVENT_AFTER_IMPORT = 'afterImport';

    /**
     * @param SoapResultWrapper $fileInfo
     *
     * @return Directory
     * @throws \yii\base\InvalidConfigException
     */
    protected function loadFile(SoapResultWrapper $fileInfo)
    {
        return $this->loader->loadInitFile($fileInfo);
    }

    /**
     * Import fias data in base
     *
     * @param null|string $file
     *
     * @param null|int $version
     *
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\console\Exception
     */
    public function import($file = null, $version = null)
    {

        $directory = $this->getDirectory($this->loader->getVersionFileInfo($version), $file);

        $this->db->createCommand('SET foreign_key_checks = 0;')->execute();

        $this->trigger(self::EVENT_BEFORE_IMPORT, new ImportEvent($this->db));

        $this->dropIndexes();

        $this->dropData();

        $this->importAddressObjectLevel($directory);

        $this->importAddressObject($directory);

        $this->importHouse($directory);

        $this->addIndexes();

        $this->trigger(self::EVENT_AFTER_IMPORT, new ImportEvent($this->db));

        $this->saveLog($directory->getVersionId());

        $this->db->createCommand('SET foreign_key_checks = 1;')->execute();

    }

    /**
     * Import fias address object
     *
     * @param Directory $directory
     *
     * @throws Exception
     * @throws \yii\console\Exception
     * @throws \yii\base\InvalidConfigException
     */
    private function importAddressObject(Directory $directory)
    {
        Console::output('Импорт адресов объектов');
        FiasAddressObject::import(new XmlReader(
            $directory->getAddressObjectFile(),
            FiasAddressObject::XML_OBJECT_KEY,
            array_keys(FiasAddressObject::getXmlAttributes()),
            FiasAddressObject::getXmlFilters()
        ), null, $this->loader->fileDirectory);
    }

    /**
     * Import fias house
     *
     * @param Directory $directory
     *
     * @throws Exception
     * @throws \yii\console\Exception
     * @throws \yii\base\InvalidConfigException
     */
    private function importHouse(Directory $directory)
    {
        Console::output('Импорт домов');
        FiasHouse::import(new XmlReader(
            $directory->getHouseFile(),
            FiasHouse::XML_OBJECT_KEY,
            array_keys(FiasHouse::getXmlAttributes()),
            FiasHouse::getXmlFilters()
        ), null, $this->loader->fileDirectory);
    }

    /**
     * Import fias address object levels
     *
     * @param Directory $directory
     *
     * @throws Exception
     * @throws \yii\console\Exception
     * @throws \yii\base\InvalidConfigException
     */
    private function importAddressObjectLevel(Directory $directory)
    {
        Console::output('Импорт типов адресных объектов (условные сокращения и уровни подчинения)');
        FiasAddressObjectLevel::import(
            new XmlReader(
                $directory->getAddressObjectLevelFile(),
                FiasAddressObjectLevel::XML_OBJECT_KEY,
                array_keys(FiasAddressObjectLevel::getXmlAttributes()),
                FiasAddressObjectLevel::getXmlFilters()
        ), null, $this->loader->fileDirectory);
    }


    /**
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
    protected function dropForeignKeyIfExists($name, $table)
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
    protected function dropIndexIfExists($name, $table)
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
     */
    protected function dropPrimaryKeyIfExists($name, $table)
    {
        try {
            $this->db->createCommand()->dropPrimaryKey($name, $table)->execute();
        } catch (Exception $ignored) {

        }
    }



    protected function dropData()
    {
        FiasAddressObjectLevel::deleteAll();
        FiasAddressObject::deleteAll();
        FiasHouse::deleteAll();
    }

}