<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.03.16 14:09
 */

namespace design\modules\content\models;

use yii\base\Model;
use design\modules\content\interfaces\PlaceholderInterface;
use design\modules\content\traits\PlaceholderTrait;

abstract class StaticPlaceholder extends Model implements PlaceholderInterface
{
    use PlaceholderTrait;

    /** @var string */
    private $name;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }
}
