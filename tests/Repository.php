<?php

/*
 * This file is part of ScientiaMobile WURFL PHP API
 *
 * (c) ScientiaMobile, Inc. http://www.scientiamobile.com
 *
 * This software package is the property of ScientiaMobile Inc. and is licensed commercially according to a contract
 * between the Licensee and ScientiaMobile Inc. (Licensor).
 * If you represent the Licensee, please refer to the licensing agreement which has been signed between the two parties.
 * If you do not represent the Licensee, you are not authorized to use this software in any way.
 */

namespace ScientiaMobile\WURFL\Repositories;

/**
 * Repository Class
 * Provides all the information for the WURFL DB Repository currently used by the API
 *
 * This file is generated automatically by WURFL PHP API and it is overwritten every time the WURFL DB is updated.
 *
 * @see ScientiaMobile\WURFL\Repositories\RepositoryManager
 */
final class Repository
{
    private static $id = '935224f5bf6665e488d60891ea5f486d';
    private static $storage_type = 'ScientiaMobile\WURFL\Storage\FileStorage';
    private static $version = 'for WURFL API 1.8.0.0 evaluation, db.scientiamobile.com - 2016-06-17 15:30:14';
    private static $last_updated = '2016-06-17 15:33:19 -0400';
    private static $history = array (
  '935224f5bf6665e488d60891ea5f486d' => 
  array (
    'updated' => 'Thu, 04 Aug 2016 16:40:33 GMT',
    'wurfl_db' => '/Users/albert/Projects/ACFWurflBundle/tests/Resources/wurfl/wurfl_base.xml',
  ),
);

    private function __construct()
    {
    }

    /**
     * Returns a string specific to the WURFL DB to be used for checking internal state.
     * @internal
     * @return string
     */
    public static function getID()
    {
        return self::$id;
    }

    /**
     * Returns the WURFL DB Version
     * @return string
     */
    public static function getVersion()
    {
        return self::$version;
    }

    /**
     * Returns the WURFL DB last updated value
     * @return string
     */
    public static function getLastUpdated()
    {
        return self::$last_updated;
    }

    /**
     * Returns the storage type
     * @return array
     */
    public static function getStorageType()
    {
        return self::$storage_type;
    }

    /**
     * Returns the updater history
     * @return array
     */
    public static function getHistory()
    {
        return self::$history;
    }
}
