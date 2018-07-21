<?php


class FiasCest
{
    public function _before(FunctionalTester $I)
    {
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
        \Yii::$app->runAction('fias/install');
    }


    public function testUpdate(FunctionalTester $I)
    {
        $I->haveFixtures([
            \solbianca\fias\tests\_fixtures\FiasUpdateLogFixture::class,
            \solbianca\fias\tests\_fixtures\FiasAddressObjectFixture::class,
            \solbianca\fias\tests\_fixtures\FiasHouseFixture::class
        ]);
        \Yii::$app->runAction('fias/update');
    }
}
