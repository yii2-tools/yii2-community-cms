<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.03.16 13:34
 */

namespace design\modules\content\traits;

use site\modules\design\helpers\ModuleHelper;
use Yii;
use yii\helpers\VarDumper;
use yii\base\NotSupportedException;
use yii\caching\ArrayCache;
use yii\tools\exceptions\RepeatException;
use design\modules\content\helpers\PlaceholderHelper;
use design\modules\content\interfaces\PlaceholderInterface;

/**
 * Base implementation of PlaceholderInterface methods for concrete placeholder classes
 * @package design\modules\content\traits
 */
trait PlaceholderTrait
{
    /** @var PlaceholderInterface[] */
    protected $childs;

    /** @var int */
    protected $evaluateAttempts = 2;

    /**
     * @see PlaceholderInterface
     */
    abstract public function getName();

    /**
     * Returns content placeholder params array for evaluate action
     * @return array
     */
    protected function configure()
    {
        $params = [];
        if ($this->isChildSupported()) {
            $childs = $this->getChilds();
            foreach ($childs as $child) {
                if ($content = $this->evaluateChild($child)) {
                    $params[$child->getName()] = $content;
                }
            }
        }

        return $params;
    }

    /**
     * Evaluate child action
     * @param PlaceholderInterface $child
     * @return string
     */
    protected function evaluateChild(PlaceholderInterface $child)
    {
        return $child->evaluate();
    }

    /**
     * @see PlaceholderInterface
     * @return int
     */
    public function getStatus()
    {
        return PlaceholderHelper::STATUS_ACTIVE;
    }

    /**
     * @see PlaceholderInterface
     * @return bool
     */
    public function activate()
    {
        return true;
    }

    /**
     * Generates placeholder content for replacing template variables
     * @return string
     */
    final public function evaluate()
    {
        /** @var \design\modules\content\Module $module */
        $module = Yii::$app->getModule(ModuleHelper::DESIGN_CONTENT);
        $requestCache = $module->getCache();

        try {
            $name = $this->getName();

            // Caching for current request only
            if (($result = $requestCache->get($name)) !== false) {
                return $result;
            }

            if (PlaceholderHelper::STATUS_ACTIVATION_REQUIRED === $this->getStatus()) {
                // Normally, not going here.
                // All placeholder activation actions already performed at bootstrap time.
                $this->activate();
            }

            $params = $this->configure();
            $result = $this->evaluateAttempt($params);
            $requestCache->set($name, $result);

            return $result;
        } catch (\Exception $e) {
            Yii::error('Content placeholder evaluate failed' . PHP_EOL . VarDumper::dumpAsString($e), __METHOD__);

            return $this->renderError();
        }
    }

    /**
     * @param array $params
     * @return string
     * @throws \Exception
     */
    final protected function evaluateAttempt(array $params)
    {
        try {
            --$this->evaluateAttempts;

            return $this->evaluateInternal($params);
        } catch (RepeatException $e) {
            if ($this->evaluateAttempts > 0) {
                return $this->evaluateAttempt($params);
            }

            throw new \Exception('Too many unsuccessful evaluate attempts)', 500, $e);
        }
    }

    /**
     * Actual evaluate logic, should be implemented within concrete content placeholder class
     * @param array $params
     * @return string
     */
    abstract protected function evaluateInternal(array $params);

    /**
     * Returns error text in case of failed evaluate action
     * @return string
     */
    protected function renderError()
    {
        return Yii::t('errors', 'Internal error');
    }

    /**
     * @see PlaceholderInterface
     */
    public function isChildOnly()
    {
        return false;
    }

    /**
     * @see PlaceholderInterface
     */
    public function isChildSupported()
    {
        return true;
    }

    /**
     * Base implementation of addChild from PlaceholderInterface
     * @param PlaceholderInterface $child
     * @throws \yii\base\NotSupportedException
     * @see PlaceholderInterface
     */
    public function addChild(PlaceholderInterface $child)
    {
        if (!$this->isChildSupported()) {
            throw new NotSupportedException(get_class($this) . " can't have childs (isChildSupported policy)");
        }
        $this->ensureChilds();
        $this->childs[$child->getName()] = $child;
    }

    /**
     * Base implementation of getChilds from PlaceholderInterface
     * @return PlaceholderInterface[]
     * @throws \yii\base\NotSupportedException
     * @see PlaceholderInterface
     */
    public function getChilds()
    {
        if (!$this->isChildSupported()) {
            throw new NotSupportedException(get_class($this) . " can't have childs (isChildSupported policy)");
        }
        $this->ensureChilds();
        return $this->childs;
    }

    /**
     * Base implementation of ensureChilds from PlaceholderInterface
     * @see PlaceholderInterface
     */
    protected function ensureChilds()
    {
        if (!isset($this->childs)) {
            $this->childs = [];
        }
        $this->ensureChildsReady();
    }

    /**
     * Ensuring each child ready for evaluating as parent's param
     * @throws \Exception
     */
    protected function ensureChildsReady()
    {
        foreach ($this->childs as $child) {
            $this->ensureChildReady($child);
        }
    }

    /**
     * Ensuring target child ready for evaluating as parent's param
     * @param PlaceholderInterface $child
     * @throws \Exception
     */
    protected function ensureChildReady(PlaceholderInterface $child)
    {
        if (PlaceholderHelper::STATUS_ACTIVE !== $child->getStatus()) {
            throw new \Exception("Child placeholder not activated: '{$child->getName()}'");
        }
    }
}
