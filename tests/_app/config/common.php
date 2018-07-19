<?php

return [
    'id' => 'yii2-fias',
    'basePath' => __DIR__ . '/..',
    'aliases'             => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => __DIR__ . '/../../../vendor',
    'components' => [
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host=mysql;dbname=yii2-fias',
            'username' => 'root',
            'password' => '1',
            'charset' => 'utf8'
        ],
    ],
];
