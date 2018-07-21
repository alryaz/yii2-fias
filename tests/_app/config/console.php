<?php

return [
    'bootstrap' => ['fias'],
    'controllerMap' => [
        'migrate' => [
            'class'         => \yii\console\controllers\MigrateController::class,
            'migrationPath' => __DIR__ . '/../../../migrations'
        ],
        'fias' => [
            'class' => \solbianca\fias\console\controllers\FiasController::class
        ]
    ],
    'modules' => [
        'fias' => [
            'class' => \solbianca\fias\Module::class,
            'components' => [
                'loader' => [
                    'class' => \solbianca\fias\console\base\Loader::class,
                    'wsdlUrl' => 'http://fias-stub/DownloadService.php?WSDL',
                    'fileDirectory' => '@runtime/fias'
                ],
                'importFias' => [
                    'class' => \solbianca\fias\console\components\ImportFiasComponent::class
                ],
                'updateFias' => [
                    'class' => \solbianca\fias\console\components\UpdateFiasComponent::class
                ]
            ]
        ]
    ]
];

