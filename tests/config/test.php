<?php

return [
    'id' => 'yii2-fias',
    'basePath' => __DIR__ . '/..',
    'components' => [
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host=mysql;dbname=yii2-fias',
            'username' => 'root',
            'password' => '1',
            'charset' => 'utf8'
        ],
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => \yii\console\controllers\MigrateController::class,
            'migrationPath' => __DIR__ . '/../../migrations'
        ]
    ]
];

