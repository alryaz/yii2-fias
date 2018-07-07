<?php

/**
 * Base model for ImportModel and UpdateModel
 *
 */

namespace solbianca\fias\console\models;

use Yii;
use yii\base\Model;
use yii\console\Exception;
use yii\db\Connection;
use yii\helpers\Console;
use solbianca\fias\Module as Fias;
use solbianca\fias\console\base\Loader;
use solbianca\fias\models\FiasUpdateLog;

abstract class BaseModel extends Model
{
    /**
     * @var Loader
     */
    protected $loader;

    /**
     * @var \solbianca\fias\console\base\SoapResultWrapper
     */
    protected $fileInfo;

    /**
     * @var string|null
     */
    protected $file;

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var \solbianca\fias\console\base\Directory
     */
    protected $directory;

    /**
     * Fias base version
     *
     * @var string
     */
    protected $versionId;

    /**
     * @inherit
     *
     * @param Loader $loader
     * @param string|null $file
     * @param array $config
     */
    public function __construct(Loader $loader, $file = null, $config = [])
    {
        parent::__construct($config);

        $this->db = Fias::db();

        $this->loader = $loader;
        $this->file = $file;

        $this->fileInfo = $loader->getLastFileInfo();
        $this->directory = $this->getDirectory($file, $this->loader, $this->fileInfo);
        $this->versionId = $this->getVersion($this->directory);
    }

    abstract function run();

    /**
     * Save log
     */
    protected function saveLog()
    {
        Console::output(Yii::$app->formatter->asDateTime(time(), 'php:Y-m-d H:i:s').' '.  'Логируем.');
        if (!$log = FiasUpdateLog::findOne(['version_id' => $this->versionId])) {
            $log = new FiasUpdateLog();
            $log->version_id = $this->versionId;
        }

        $log->created_at = time();
        $log->save(false);
    }

    /**
     * Try to use given file else download full file
     *
     * @param $file
     * @param $loader Loader
     * @param $fileInfo \solbianca\fias\console\base\SoapResultWrapper
     * @return \solbianca\fias\console\base\Directory
     * @throws Exception
     */
    protected function getDirectory($file, $loader, $fileInfo)
    {
        if (null !== $file) {
            if (!file_exists($file)) {
                throw new Exception("File {$file} do not exist.");
            }
            $directory = $loader->wrapDirectory(Yii::getAlias($file));
        } else {
            Console::output(Yii::$app->formatter->asDateTime(time(), 'php:Y-m-d H:i:s').' '.  "Скачивание последней версии полной БД ФИАС");
            $directory = $loader->loadInitFile($fileInfo);
        }

        return $directory;
    }

    /**
     * Get fias base version
     *
     * @param $directory \solbianca\fias\console\base\Directory
     * @return string
     */
    protected function getVersion($directory)
    {
        return $directory->getVersionId();
    }
}
