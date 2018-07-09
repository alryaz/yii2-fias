<?php

/**
 * Base model for ImportModel and UpdateModel
 *
 */

namespace solbianca\fias\console\models;

use solbianca\fias\console\base\Loader;
use solbianca\fias\models\FiasUpdateLog;
use solbianca\fias\Module;
use Yii;
use yii\base\Model;
use yii\console\Exception;
use yii\db\Connection;
use yii\di\Instance;

abstract class BaseModel extends Model
{
    /**
     * @var string|null
     */
    public $file;

    /**
     * @var Loader
     */
    protected $loader;

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var \solbianca\fias\console\base\SoapResultWrapper
     */
    protected $fileInfo;

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
     * @throws \yii\base\InvalidConfigException
     * @throws Exception
     */
    public function init()
    {
        parent::init();
        $this->loader    = Instance::ensure('loader', Loader::class, Module::getInstance());
        $this->db        = Instance::ensure('db', Connection::class, Module::getInstance());
        $this->fileInfo  = $this->loader->getLastFileInfo();
        $this->directory = $this->getDirectory($this->file, $this->loader, $this->fileInfo);
        $this->versionId = $this->getVersion($this->directory);
    }


    abstract function run();

    /**
     * Save log
     */
    protected function saveLog()
    {
        if ( ! $log = FiasUpdateLog::findOne(['version_id' => $this->versionId])) {
            $log             = new FiasUpdateLog();
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
     *
     * @return \solbianca\fias\console\base\Directory
     * @throws Exception
     */
    protected function getDirectory($file, $loader, $fileInfo)
    {
        if (null !== $file) {
            if ( ! file_exists($file)) {
                throw new Exception("File {$file} do not exist.");
            }
            $directory = $loader->wrapDirectory(Yii::getAlias($file));
        } else {
            $directory = $loader->loadInitFile($fileInfo);
        }

        return $directory;
    }

    /**
     * Get fias base version
     *
     * @param $directory \solbianca\fias\console\base\Directory
     *
     * @return string
     * @throws Exception
     */
    protected function getVersion($directory)
    {
        return $directory->getVersionId();
    }
}