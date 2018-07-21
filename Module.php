<?php

namespace solbianca\fias;



/**
 * Class Module
 * @package solbianca\fias
 *
 * @property string $directory
 */
class Module extends \yii\base\Module
{

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
