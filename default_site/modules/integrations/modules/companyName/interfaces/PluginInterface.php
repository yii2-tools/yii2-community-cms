<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 17.04.16 23:09
 */

namespace integrations\modules\companyName\interfaces;

/**
 * Interface suited for working with raw integration data in context of plugins.
 * Object that implements it must parse integration data for needed fields.
 *
 * @package integrations\modules\companyName\interfaces
 */
interface PluginInterface
{
    /**
     * Returns plugin name extracted from integration data.
     * @return string
     */
    public function getName();

    /**
     * Returns plugin version extracted from integration data.
     * @return string
     */
    public function getVersion();

    /**
     * Returns plugin status extracted from integration data.
     * @return string
     */
    public function getStatus();

    /**
     * Returns plugin key extracted from integration data.
     * @return string
     */
    public function getKey();

    /**
     * Returns plugin API key extracted from integration data.
     * @return string
     */
    public function getApiKey();

    /**
     * Returns path to directory with plugin's source files.
     * @return string
     */
    public function getSourceDir();

    /**
     * Returns path to file that should be called BEFORE query.
     * @param string $type e.g. page, container
     * @param string $queryKey
     * @return string|false boolean false if prepare file for query doesn't exists
     */
    public function getPrepareFile($type, $queryKey);

    /**
     * Returns path to file that should be called AFTER query.
     * @param string $type e.g. page, container
     * @param string $queryKey
     * @return string|false boolean false if postpare file for query doesn't exists
     */
    public function getPostpareFile($type, $queryKey);

    /**
     * Returns plugin config.
     * @param string $type e.g. page, container
     * @return array
     */
    public function getConfig($type);


    /**
     * Returns raw integration data.
     * @return array
     */
    public function getIntegrationData();
}
