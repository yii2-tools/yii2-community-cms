<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 26.03.16 19:07
 */

namespace app\modules\integrations\components;

use Yii;
use app\helpers\ModuleHelper;
use yii\tools\interfaces\ActivateInterface;
use yii\tools\interfaces\CompatibleInterface;
use yii\tools\interfaces\OwnableInterface;
use design\modules\content\models\WidgetPlaceholder;

abstract class Widget extends \yii\base\Widget implements CompatibleInterface, ActivateInterface, OwnableInterface
{
    /** @var WidgetPlaceholder */
    protected $owner;

    private $active = false;
    private $errors = [];

    final public function run()
    {
        Yii::trace("Running widget for placeholder '{$this->owner->getName()}'", __METHOD__);

        $this->ensureReady();

        if (!empty($this->errors)) {
            return $this->renderErrors();
        }

        return $this->runInternal();
    }

    protected function ensureReady()
    {
        if (!$this->isCompatible()) {
            $this->errors[] = 'not compatible';
        } elseif (!$this->isActive()) {
            $this->errors[] = 'not activated';
        }
    }

    public function isCompatible()
    {
        return true;
    }

    public function isActive()
    {
        return $this->active;
    }

    public function setIsActive($active)
    {
        $this->active = $active;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return string generated widget's html code
     */
    abstract protected function runInternal();

    protected function renderErrors()
    {
        //@todo
        // надо удалить из массива $erros ошибки при рендеринге
        // логика аналогична flush в session
    }

    public function activate()
    {
        Yii::trace("Activating widget component for placeholder '{$this->owner->getName()}'", __METHOD__);

        if (!$this->canActivate()) {
            return false;
        }

        return Yii::$app
            ->getModule(ModuleHelper::INTEGRATIONS)
            ->placeholders
            ->activatePlaceholder($this->getOwner());
    }

    protected function canActivate()
    {
        return Yii::$app
            ->getModule(ModuleHelper::INTEGRATIONS)
            ->placeholders
            ->canActivatePlaceholder($this->getOwner());
    }
}
