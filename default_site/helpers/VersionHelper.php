<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 07.04.16 16:54
 */

namespace app\helpers;

use Yii;

/**
 * @package app\helpers
 * @since 2.0.0
 */
class VersionHelper
{
    const PATTERN = '/^\d+(?:\.\d+)+$/';

    /**
     * Returns true whenever $version exists in [min, max] range
     * extracted from $versionConstraint pattern.
     *
     * @param int $version
     * @param string $versionConstraint
     * @return boolean
     */
    public static function checkVersionConstraint($version, $versionConstraint)
    {
        $version = Yii::$app->getFormatter()->asVersionInteger($version);
        list($min, $max) = static::exposeVersionConstraint($versionConstraint);

        return $min === $max ? $min === $version : $min <= $version && $version < $max;
    }

    /**
     * Returns array of min and max values extracted from version constraint pattern.
     *
     * This method designed to be used with CompatibleInterface for determining component's
     * compatibility with application's (or another component's) version.
     *
     * Supported Constraints:
     * – Exact (2.0.0) => [200, 200]
     * – Wildcard (2.0.*) => [200, 210]
     *
     * @param string $versionConstraint version constraint pattern
     * @return array [min, max] exposed version constraint range
     * @link https://getcomposer.org/doc/articles/versions.md
     */
    public static function exposeVersionConstraint($versionConstraint)
    {
        // @todo implement real logic
        return [0, 99999];
    }
}
