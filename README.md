Документация по модулю fias
============================

Модуль fias предназначен для работы с государственным адресным реестром http://fias.nalog.ru/ 

Отличия форка от оригинального модуля
-------------------------------------

1. Работает только с MySQL (используются операторы REPLACE INTO, TRUNCATE TABLE).
2. Добавлена возможность настроить отдельное подключение к БД в настройках модуля.
3. Обновления применяются последовательно от версии к версии.
4. Компоненты, выполняющие импорт, внедряются в модуль, т.е. могут быть переопределены. См. конфигурвцию модуля.
5. Добавлено окружение на docker для тестирования и функциональные тесты на команды установки/обновления ФИАС.


Структура модуля
-------------------

    actions             содержит действия               
    console             содержит логику для работы приложения в консоли
        base            содержит модели необходимые для работы модуля в консоли
        controllers     содержит контроллер консольных команд
        models          содержит модели для работы с данными в консоли
        traits          содержит трейты
    controllers         содержит основные контроллеры модуля
    helpers             содержит классы хелперы
    models              сожержит основные модели модуля
    searches            содержит модели поиска
    widgets             содержит виджеты модуля
    Module.php          базовый класс модуля
    
Зависимости
-------------------

Модулю для работы нужно официальное jquery ui расширение (yiisoft/yii2-jui).

Установка
-------------------
Установить модуль с помощью композера:
````
    composer require solbianca/yii2-fias "dev-master"
````

 Применить миграции:
 ````
     php yii migrate/up --migrationPath=@vendor/solbianca/yii2-fias/migrations
 ````
Настройки
-------------------

В файле конфига необходимо подключить модуль:
    
````
    'modules' => [
        'fias' => [
            'class' => \solbianca\fias\Module::class,
            'components' => [
                'loader' => [
                    'class' => \solbianca\fias\console\base\Loader::class,
                ],
                'importFias' => [
                    'class' => \solbianca\fias\console\components\ImportFiasComponent::class,
                ],
                'updateFias' => [
                    'class' => \solbianca\fias\console\components\UpdateFiasComponent::class
                ]
            ]
        ],
        ....
    ],
```` 

Насройка отдельного подключения к БД:

````
    'modules' => [
        'fias' => [
            'class' => \solbianca\fias\Module::class,
            'components' => [
                'loader' => [
                    'class' => \solbianca\fias\console\base\Loader::class,
                ],
                'importFias' => [
                    'class' => \solbianca\fias\console\components\ImportFiasComponent::class,
                ],
                'updateFias' => [
                    'class' => \solbianca\fias\console\components\UpdateFiasComponent::class
                ],
                'db' => [
                    'class' => \yii\db\Connection::class
                ]
            ]
        ],
        ....
    ],

````

Задать карту контроллеров:

````
'controllerMap' => [
    'fias' => [
        'class' => 'solbianca\fias\console\controllers\FiasController'
    ]
],
````

Модулю можно указать директорию, в которую буду скачиваться архивы/распаковываться базы данных.
По умолчанию пытается скачивать/распаковывать в папку @app/runtime/fias

````
    'modules' => [
        ....
        'fias' => [
            'class' => \solbianca\fias\Module::class,
            'components' => [
                'loader' => [
                    'class' => \solbianca\fias\console\base\Loader::class,
                    'fileDirectory' => '@path_alias/to/directory'
                ],
                'importFias' => [
                    'class' => \console\components\ImportFiasComponent::class
                ],
                'updateFias' => [
                    'class' => \solbianca\fias\console\components\UpdateFiasComponent::class
                ]
            ]
        ],
        ....
    ],
````
  
Консольные команды
-------------------

Для инициализации базы данных fias необходимо набрать команду:
````
    php yii fias/install
````
Данный способ требует много времени, так как приложение сначало скачает архив на 3.5 гигабайта, затем его извлечет и только затем импортирует данные.

Более предпочтительный способ инициализации базы. Предварительно скачиваем базу, распаковываем, заливаем на сервер и указываем путь до нее в консольной команде.
````
    php yii fias/install /path/to/files
````
В силу того что база имеет большой размер (около 20 гигабайт), импорт полной базы может продолжаться длительное время, несколько часов. 

Для обновления данных базы fias используется команда. Приложение сммотрит последнюю версию данных на сервере и версию импоритрованную на сервер.
Если они разлисны, скачивает последнюю версию delta_fias и применяет ее.
````
    php yii fias/update
````

Очистить директорию для скачки/распаковывания файлов (по умолчанию @app/runtime/fias):
````
    php yii fias/clear-directory
````

Виджет
-----------------------

Для того что бы использовать виджет необходимо в нужном файле представления прописать:

````
<?= app\modules\fias\widgets\autocomplete\Autocomplete::widget() ?>
````

Запуск тестов
-------------

Перед запуском необходимо установить docker и docker-compose.


```bash

./app.sh

docker-compose exec -u www-data php bash

./yii migrate

./vendor/bin/codecept run

```
