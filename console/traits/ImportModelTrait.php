<?php
namespace solbianca\fias\console\traits;

use solbianca\fias\models\FiasModelInterface;
use yii\db\ActiveRecord;
use yii\helpers\Console;
use solbianca\fias\console\base\XmlReader;

/**
 * @mixin ActiveRecord
 * @mixin FiasModelInterface
 */
trait ImportModelTrait
{
    /**
     * @param XmlReader $reader
     * @param array|null $attributes
     * @param $directory
     *
     * @throws \yii\db\Exception
     */
    public static function import(XmlReader $reader, $attributes = null, $directory)
    {
        if (is_null($attributes)) {
            $attributes = static::getXmlAttributes();
        }
        static::processImportRows($reader, $attributes, $directory);
        static::importCallback();
    }


    /**
     * @param XmlReader $reader
     * @param array $attributes
     * @param $directory
     *
     * @throws \yii\db\Exception
     */
    protected static function processImportRows(XmlReader $reader, $attributes, $directory)
    {
        $count = 0;
        $tableName = static::tableName();
        $attributes = array_values($attributes);

        while ($data = $reader->getRows()) {
            $rows = [];
            foreach ($data as $row) {
                $row = array_combine($attributes, array_values($row));
                if (!empty(array_filter($row))) {
                    $rows[] = $row;
                }
            }
            if (!empty($rows)) {
                $count += static::getDb()
                    ->createCommand()
                    ->batchInsert($tableName, $attributes, $rows)
                    ->execute();
                Console::output("Inserted {$count} rows");
            }
        }
    }

    /**
     * After import callback
     */
    public static function importCallback()
    {
    }
}
