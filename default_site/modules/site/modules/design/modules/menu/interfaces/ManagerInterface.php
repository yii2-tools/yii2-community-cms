<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 15.04.16 4:51
 */

namespace design\modules\menu\interfaces;

use yii\tools\interfaces\ContentGeneratorInterface;

interface ManagerInterface
{
    /**
     * Performs generator registration for content generate stage.
     *
     * @param ContentGeneratorInterface $generator
     * @return mixed
     */
    public function register(ContentGeneratorInterface $generator);

    /**
     * Used by site components which provide some additional content to be rendered in menu.
     *
     * @param string $content html code of additional content for menu
     */
    public function addContent($content);

    /**
     * Returns array of content registered as additional menus content
     * (in register order, FIFO)
     *
     * @return array
     */
    public function getContent();
}
