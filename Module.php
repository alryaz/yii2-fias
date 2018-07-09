<?php

namespace solbianca\fias;

use solbianca\fias\console\base\Loader;

/**
 * Class Module
 * @package solbianca\fias
 *
 * @property string $directory
 */
class Module extends \yii\base\Module
{

    public $components = [
        'loader' => [
            'class'         => Loader::class,
            'wsdlUrl'       => 'http://fias.nalog.ru/WebServices/Public/DownloadService.asmx?WSDL',
            'fileDirectory' => '@app/runtime/fias',
        ]
    ];

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @param string $value
     */
    public function setDirectory($value)
    {
        $this->directory = $value;
    }
}
