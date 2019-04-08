<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 17.04.16 22:36
 */

namespace site\modules\plugins\components;

use yii\base\Component;
use site\modules\plugins\interfaces\ContextInterface;

class Context extends Component implements ContextInterface
{
    const TYPE_PAGE     = 'page';
    const TYPE_WIDGET   = 'container';

    /**
     * @var int
     */
    protected $type;

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
