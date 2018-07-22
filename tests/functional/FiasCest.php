<?php


use solbianca\fias\models\FiasAddressObject;
use solbianca\fias\models\FiasHouse;
use solbianca\fias\models\FiasUpdateLog;

class FiasCest
{
    public function _before(FunctionalTester $I)
    {
        \yii\helpers\FileHelper::removeDirectory(__DIR__ . '/../_app/runtime/fias');
    }

    public function _after(FunctionalTester $I)
    {
    }

    public function testInstall(FunctionalTester $I)
    {
        $I->haveFixtures([
            \solbianca\fias\tests\_fixtures\FiasUpdateLogFixture::class,
            \solbianca\fias\tests\_fixtures\FiasAddressObjectFixture::class,
            \solbianca\fias\tests\_fixtures\FiasHouseFixture::class,
            \solbianca\fias\tests\_fixtures\FiasAddressObjectLevelFixture::class
        ]);

        $I->dontSeeRecord(FiasUpdateLog::class, ['version_id' => 3]);
        $I->dontSeeRecord(FiasAddressObject::class, ['title' => 'Москва']);

        \Yii::$app->runAction('fias/install');

        $I->seeRecord(FiasUpdateLog::class, ['version_id' => 3]);
        $I->seeRecord(FiasAddressObject::class, ['title' => 'Москва']);


    }


    public function testUpdate(FunctionalTester $I)
    {
        $I->haveFixtures([
            \solbianca\fias\tests\_fixtures\FiasUpdateLogFixture::class,
            \solbianca\fias\tests\_fixtures\FiasAddressObjectFixture::class,
            \solbianca\fias\tests\_fixtures\FiasHouseFixture::class
        ]);

        \Yii::$app->runAction('fias/install', [null, '1']);

        $I->seeRecord(FiasUpdateLog::class, ['version_id' => 1]);
        $I->seeRecord(FiasAddressObject::class, ['title' => 'Москва']);
        $I->dontSeeRecord(FiasUpdateLog::class, ['version_id' => 3]);

        \Yii::$app->runAction('fias/update');

        $I->seeRecord(FiasAddressObject::class, ['title' => 'Авачинская']);
        $I->dontSeeRecord(FiasAddressObject::class, ['title' => 'гк 345']);
        $I->seeRecord(FiasHouse::class, ['number' => '84А']);
        $I->dontSeeRecord(FiasHouse::class, ['number' => '12А']);
        $I->seeRecord(FiasUpdateLog::class, ['version_id' => 3]);

        \Yii::$app->runAction('fias/update', ['2']);

        $I->seeRecord(FiasAddressObject::class, ['title' => 'Авачинская']);
        $I->dontSeeRecord(FiasAddressObject::class, ['title' => 'гк 345']);
        $I->seeRecord(FiasHouse::class, ['number' => '84А']);
        $I->dontSeeRecord(FiasHouse::class, ['number' => '12А']);
        $I->seeRecord(FiasUpdateLog::class, ['version_id' => 3]);

    }
}
