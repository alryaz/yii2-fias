<?php
namespace solbianca\fias\console\controllers;

use solbianca\fias\console\base\Loader;
use solbianca\fias\console\models\UpdateModel;
use solbianca\fias\helpers\FileHelper;
use solbianca\fias\console\models\ImportModel;
use yii\console\Controller;
use yii\di\Instance;
use yii\helpers\Console;

class FiasController extends Controller
{
    /**
     * Init fias data in base.
     * If given parameter $file is null try to download full file, else try to use given file.
     *
     * @param string|null $file
     * @throws \Exception
     */
    public function actionInstall($file = null)
    {
        $importModel = Instance::ensure([
            'file' => $file
        ], ImportModel::class, $this->module);
        $importModel->run();
    }

    /**
     * Update fias data in base
     *
     * @param string|null $file
     *
     * @throws \Exception
     */
    public function actionUpdate($file = null)
    {
        $updateModel = Instance::ensure([
            'file' => $file
        ], UpdateModel::class, $this->module);
        $updateModel->run();
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * Clear directory for upload/extract files
     */
    public function actionClearDirectory()
    {
        /** @var Loader $loader */
        $loader = Instance::ensure('loader', Loader::class, $this->module);
        $directory = $loader->fileDirectory;
        FileHelper::clearDirectory($directory);
        Console::output("Очистка директории '{$directory}' завершена.");
    }

}