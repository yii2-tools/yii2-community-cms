<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 09.03.16 14:54
 */

namespace app\helpers;

use wapmorgan\UnifiedArchive\UnifiedArchive;

/**
 * Class ArchiveHelper
 * @package app\helpers
 */
class ArchiveHelper
{
    /**
     * @param string $filename
     * @return null|UnifiedArchive
     * @throws \LogicException
     */
    public static function open($filename)
    {
        if (!($archive = UnifiedArchive::open($filename))) {
            throw new \LogicException("Can't open '$filename' as archive");
        }

        return $archive;
    }

    /**
     * @param string $filename
     * @param string $destpath
     * @param string $node
     * @throws \LogicException
     */
    public static function extract($filename, $destpath, $node = '/')
    {
        if (!static::open($filename)->extractNode($destpath, $node)) {
            throw new \LogicException("Can't extract '$filename' to '$destpath'");
        }
    }

    /**
     * @param string|Array $source
     * @param string $archiveName
     * @return array|bool|int
     * @link https://github.com/wapmorgan/UnifiedArchive/blob/master/doc.archiveNodes.md
     */
    public static function create($source, $archiveName)
    {
        $archiveData = UnifiedArchive::archiveNodes($source, $archiveName, true);
        $files = $archiveData['files'];

        if (array_key_exists('/', $files)) {
            unset($files['/']);
        }

        foreach ($files as &$file) {
            $file = $source . $file;
        }

        return UnifiedArchive::archiveNodes($files, $archiveName);
    }
}
