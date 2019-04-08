<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 31.03.16 13:07
 */

namespace site\modules\design\components;

use Yii;
use yii\web\View as BaseView;
use site\modules\design\interfaces\ContextInterface;

class View extends BaseView
{
    /** @var ContextInterface */
    public $designContext;

    /**
     * @inheritdoc
     */
    public function __construct(ContextInterface $designContext, $config = [])
    {
        $this->designContext = $designContext;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function render($view, $params = [], $context = null)
    {
        $view = $this->designContext->configure($view);
        return parent::render($view, $params, $context);
    }
}
