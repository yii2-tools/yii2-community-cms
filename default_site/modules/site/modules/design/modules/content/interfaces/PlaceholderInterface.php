<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 13.03.16 17:02
 */

namespace design\modules\content\interfaces;

use yii\tools\interfaces\EvaluableInterface;

interface PlaceholderInterface extends EvaluableInterface
{
    /**
     * This method calculates placeholder's status and should return one of these values:
     *
     * 0 - inactive
     * 1 - activation required
     * 2 - active
     *
     * @return int
     */
    public function getStatus();

    /**
     * Performs placeholder activation on site (if presents) and returns result (true/false)
     * @return bool
     */
    public function activate();

    /**
     * Returns name of variable for this content placeholder
     * @return string
     */
    public function getName();

    /**
     * Returns flag value if this placeholder can have child placeholders
     * @return bool
     */
    public function isChildSupported();

    /**
     * Added childs accessable at rendering stage as params of parent placeholder
     * @param PlaceholderInterface $child
     */
    public function addChild(PlaceholderInterface $child);

    /**
     * Returns all child placeholders of this parent placeholder
     * @return PlaceholderInterface[]
     */
    public function getChilds();

    /**
     * Returns flag value if this placeholder can only be child of another placeholder
     * @return bool
     */
    public function isChildOnly();
}
