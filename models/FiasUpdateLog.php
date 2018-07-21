<?php

namespace solbianca\fias\models;

use solbianca\fias\Module;
use Yii;

/**
 * This is the model class for table "{{%fias_update_log}}".
 *
 * @property integer $id
 * @property integer $version_id
 * @property integer $created_at
 */
class FiasUpdateLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%fias_update_log}}';
    }

    /**
     * @return null|object|\yii\db\Connection
     * @throws \yii\base\InvalidConfigException
     */
    public static function getDb()
    {
        return Module::getInstance()->get('db');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['version_id', 'created_at'], 'required'],
            [['version_id', 'created_at'], 'integer'],
            [['version_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'version_id' => 'Version ID',
            'created_at' => 'Created At',
        ];
    }
}
