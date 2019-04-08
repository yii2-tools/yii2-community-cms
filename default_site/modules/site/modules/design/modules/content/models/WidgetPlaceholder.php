<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.03.16 18:14
 */

namespace design\modules\content\models;

use Yii;
use yii\tools\interfaces\OwnableInterface;
use yii\tools\interfaces\ActivateInterface;
use yii\tools\interfaces\EvaluableInterface;

class WidgetPlaceholder extends ActivePlaceholder
{
    /**
     * This object can also be instanceof CompatibleInterface if it depends on current app environment
     * Also this object can impement ActivateInterface then activate placeholder logic will be delegated to widget
     * @var \yii\base\Widget|EvaluableInterface
     */
    protected $widget;

    /**
     * @inheritdoc
     */
    public function isChildSupported()
    {
        return false;
    }

    /**
     * Setup widget component for this content placeholder
     * @param \yii\base\Widget|\yii\tools\interfaces\EvaluableInterface $widget
     */
    public function setWidget($widget)
    {
        $this->widget = $widget;
    }

    /**
     * @return \yii\tools\interfaces\EvaluableInterface|\yii\base\Widget
     */
    public function getWidget()
    {
        return $this->widget;
    }

    /**
     * @inheritdoc
     */
    public function activate()
    {
        $name = $this->getName();
        Yii::trace("Activating widget placeholder '$name'", __METHOD__);

        $this->configureComponents();

        if ($this->widget instanceof ActivateInterface && !$this->widget->isActive()) {
            if (!$this->widget->activate()) {
                return false;
            }
        }

        return parent::activate();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->configureComponents();

        return parent::configure();
    }

    /**
     * Configuration internal components of placeholder
     */
    protected function configureComponents()
    {
        $this->setWidget(unserialize($this->content));

        if ($this->widget instanceof OwnableInterface) {
            $this->widget->setOwner($this);
        }
    }

    /**
     * @inheritdoc
     */
    protected function evaluateInternal(array $params)
    {
        if ($this->widget instanceof \yii\base\Widget) {
            return $this->widget->run();
        } elseif ($this->widget instanceof EvaluableInterface) {
            return $this->widget->evaluate();
        }

        throw new \LogicException('Widget should be instanceof \yii\base\Widget or EvaluableInterface');
    }
}
