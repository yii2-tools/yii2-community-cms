<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 17.04.16 19:49
 */

namespace site\modules\plugins\interfaces;

interface BundleInterface
{
    /**
     * Returns name of plugin bundle (e.g. "events").
     *
     * @return string
     */
    public function getName();

    /**
     * Returns version of plugin bundle in string format (e.g. "2.0.0").
     *
     * @return string
     */
    public function getVersion();

    /**
     * Returns raw plugin's bundle html
     * generated for displaying in context of page, widget or another place.
     *
     * @return string
     */
    public function getHtml();

    /**
     * Registers bundle content (JS, CSS) with a view.
     *
     * @param \yii\web\View $view the view to be registered with
     * @return BundleInterface instance (self)
     */
    public function register($view);
}
