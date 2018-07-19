<?php



namespace solbianca\fias\console\components;

use solbianca\fias\console\base\Directory;
use solbianca\fias\console\base\Loader;
use solbianca\fias\console\base\SoapResultWrapper;
use solbianca\fias\models\FiasUpdateLog;
use solbianca\fias\Module;
use Yii;
use yii\base\Component;
use yii\console\Exception;
use yii\db\Connection;
use yii\di\Instance;

abstract class FiasComponent extends Component
{

    /**
     * @var Loader
     */
    protected $loader;

    /**
     * @var Connection
     */
    protected $db;


    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->loader    = Instance::ensure('loader', Loader::class, Module::getInstance());
        $this->db        = Instance::ensure('db', Connection::class, Module::getInstance());
    }


    /**
     * Save log
     *
     * @param int $versionId
     */
    final protected function saveLog($versionId)
    {
        if ( ! $log = FiasUpdateLog::findOne(['version_id' => $versionId])) {
            $log             = new FiasUpdateLog();
            $log->version_id = $versionId;
        }

        $log->created_at = time();
        $log->save(false);
    }

    /**
     * Try to use given file else download full file
     *
     * @param string|null $file
     * @param $fileInfo \solbianca\fias\console\base\SoapResultWrapper
     *
     * @return \solbianca\fias\console\base\Directory
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    final protected function getDirectory(SoapResultWrapper $fileInfo, $file = null)
    {
        if (null !== $file) {
            if ( ! file_exists($file)) {
                throw new Exception("File {$file} does not exist.");
            }
            $directory = $this->loader->wrapDirectory(Yii::getAlias($file));
        } else {
            $directory = $this->loadFile($fileInfo);
        }

        return $directory;
    }


    /**
     * @param SoapResultWrapper $fileInfo
     *
     * @return Directory
     */
    abstract protected function loadFile(SoapResultWrapper $fileInfo);

}