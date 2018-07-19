<?php


namespace solbianca\fias\tests\_app\controllers;


use mongosoft\soapserver\Action;
use solbianca\fias\tests\_app\types\DownloadFileInfo;
use yii\web\Controller;

/**
 * Служба получения обновлений
 * @package solbianca\fias\tests\_app\controllers
 */
class FiasController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'DownloadService' => [
                'class' => Action::class,
                'classMap' => [
                    'DownloadFileInfo' => DownloadFileInfo::class,
                    'DownloadService' => self::class
                ],
            ]
        ];
    }

    /**
     * @soap
     * @return \solbianca\fias\tests\_app\types\DownloadFileInfo
     */
    public function GetLastDownloadFileInfo()
    {
        $info = new DownloadFileInfo();

        return $info;
    }



    /**
     * @soap
     * @return \solbianca\fias\tests\_app\types\DownloadFileInfo[]
     */
    public function GetAllDownloadFIleInfo()
    {
        $info = new DownloadFileInfo();

        return [$info];
    }

}