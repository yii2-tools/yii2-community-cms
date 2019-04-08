<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 17.04.16 19:44
 */

namespace site\modules\plugins\interfaces;

use integrations\modules\companyName\interfaces\PluginInterface;

interface BundleManagerInterface
{
    /**
     * Returns generated plugin bundle
     * which contains information about plugin and generated html for displaying purposes
     *
     * @param PluginInterface $plugin object with information about plugin
     * @param ContextInterface $context object with information about how to display plugin for end-user
     * @return BundleInterface
     */
    public function build(PluginInterface $plugin, ContextInterface $context);

    /**
     * Returns array of html blocks
     * generated for each active plugin on site (if plugin supports such logic)
     *
     * Example:
     * Plugin events needs to be registered in site menu
     * to display accumulated points of users.
     * In this case, result array of blocks
     * will contain html code with current user's points (in plugin context)
     *
     * This method suited to be called in third-party view components and placeholders (e.g. menu manager)
     *
     * @return array
     */
    public function getMenuBlocks();

    /**
     * Returns html block generated for target active plugin
     *
     * @param PluginInterface $plugin object with information about plugin
     * @return string|false boolean false will be returned if plugin doesn't exists or inactive or doesn't
     * support logic of global page contents; html block represented as string will be returned
     * if plugin exists, active and support page blocks.
     */
    public function getMenuBlock(PluginInterface $plugin);
}
