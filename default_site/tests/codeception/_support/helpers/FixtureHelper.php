<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 15.03.16 17:14
 */

namespace tests\codeception\_support\helpers;

use Codeception\Module;
use Codeception\TestCase;
use yii\di\Instance;
use yii\test\FixtureTrait;
use yii\test\InitDbFixture;
use yii\test\ActiveFixture;
use tests\codeception\fixtures\UserFixture;
use tests\codeception\fixtures\DesignPackFixture;
use tests\codeception\fixtures\AssetFixture;
use tests\codeception\fixtures\EngineParamsFixture;
use tests\codeception\fixtures\ServiceFixture;
use tests\codeception\fixtures\ForumFixture;
use tests\codeception\fixtures\MenuFixture;
use tests\codeception\fixtures\PageFixture;

class FixtureHelper extends Module
{
    use FixtureTrait {
        loadFixtures as protected;
        fixtures as protected;
        globalFixtures as protected;
        createFixtures as protected;
        unloadFixtures as protected;
        getFixtures as public;
        getFixture as public;
    }

    /**
     * Hook be executed before test case started
     * @inheritdoc
     */
    public function _before(TestCase $test)
    {
        $this->loadFixtures();
    }

    /**
     * Hook be executed after test case finished
     * @inheritdoc
     */
    public function _after(TestCase $test)
    {
        $this->unloadFixtures();
    }

    /**
     * @inheritdoc
     */
    public function fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
            ],
            'designPacks' => [
                'class' => DesignPackFixture::className(),
            ],
            'menu' => [
                'class' => MenuFixture::className(),
            ],
            'page' => [
                'class' => PageFixture::className(),
            ],
        ];
    }

    /**
     * Shortcut method for ensuring what fixture is actual yii\test\ActiveFixture and enabling IDE support for class
     * @param string $name
     * @return \yii\test\ActiveFixture
     */
    public function getActiveFixture($name)
    {
        return Instance::ensure($this->getFixture($name), ActiveFixture::className());
    }

    /**
     * @inheritdoc
     */
    public function globalFixtures()
    {
        return [
            'db' => [
                'class' => InitDbFixture::className(),
                'initScript' => '@tests/codeception/fixtures/data/initdb.php',
            ],
            AssetFixture::className(),
            EngineParamsFixture::className(),
            ServiceFixture::className(),
            ForumFixture::className(),
        ];
    }
}