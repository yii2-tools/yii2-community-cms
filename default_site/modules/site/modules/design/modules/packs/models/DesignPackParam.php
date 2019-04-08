<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 28.03.16 22:09
 */

namespace design\modules\packs\models;

use Yii;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use app\components\params\ListParam;
use design\modules\packs\Finder;

/**
 * Class DesignPackParam
 * @package design\modules\packs\models\DesignPackParam
 */
class DesignPackParam extends ListParam
{
    /** @var Finder */
    public $finder;

    /**
     * @inheritdoc
     */
    public function __construct(Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($config);
    }

    /**
     * This param model don't have ListValue relations
     * @throws \yii\base\NotSupportedException
     * @return void
     */
    public function getListValues()
    {
        throw new NotSupportedException(__CLASS__ . " don't have actual relation with ListValue records");
    }

    /**
     * @inheritdoc
     */
    public function getListValuesArray()
    {
        return ArrayHelper::map($this->finder->findDesignPack(null, true), 'name', 'name');
    }
}
