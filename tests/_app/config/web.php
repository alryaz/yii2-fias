<?php

return [
    'controllerNamespace' => 'solbianca\fias\tests\_app\controllers',
    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                'debug/<controller>/<action>' => 'debug/<controller>/<action>',
            ],
        ],
    ]
];

