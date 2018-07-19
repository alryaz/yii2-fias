<?php

return [
    'controllerMap' => [
        'migrate' => [
            'class'         => \yii\console\controllers\MigrateController::class,
            'migrationPath' => __DIR__ . '/../../../migrations'
        ]
    ]
];

