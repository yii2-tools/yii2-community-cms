<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.03.16 17:34
 */

namespace design\modules\content\models;

use Yii;
use design\modules\content\interfaces\PlaceholderInterface;
use design\modules\content\components\Resolver;

/**
 * Wrapper skeleton for content placeholders
 * Represents view-based data used as params for evaluate action of parent placeholder
 * @package design\modules\content\models
 */
class ViewPlaceholder extends ActivePlaceholder
{
    const PLACEHOLDER_PATTERN = '\$([A-Z_]+)';

    /**
     * Names of placeholder's which actually used in view template
     * @var array
     */
    private $uses;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        /** @var Resolver $resolver */
        $resolver = Yii::$container->get(Resolver::className(), [], [
            'pattern' => static::PLACEHOLDER_PATTERN,
            'tags' => false,
        ]);

        $view = $this->getViewFile();
        $this->uses = $resolver->inspect($view);

        return parent::configure();
    }



    /**
     * @inheritdoc
     */
    protected function evaluateChild(PlaceholderInterface $child)
    {
        $name = $child->getName();

        if (in_array($name, $this->uses)) {
            return parent::evaluateChild($child);
        }

        Yii::warning("Unused child placeholder '$name' for parent '{$this->getName()}'", __METHOD__);
    }

    /**
     * @inheritdoc
     */
    protected function evaluateInternal(array $params)
    {
        $view = Yii::$app->controller->getView();
        $defaultExtension = $view->defaultExtension;
        // @todo refactor this for php 5.5+ (finally block)
        try {
            $view->defaultExtension = 'php';
            $result = Yii::$app->controller->renderPartial($this->getViewFile(), $params);
            $view->defaultExtension = $defaultExtension;
            return $result;
        } catch (\Exception $e) {
            $view->defaultExtension = $defaultExtension;
            throw $e;
        }
    }

    /**
     * Returns view filepath extracted from content field of active record
     * @return string
     */
    protected function getViewFile()
    {
        return Yii::$app->getFormatter()->asString($this->content);
    }
}
