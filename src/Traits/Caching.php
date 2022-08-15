<?php

declare(strict_types=1);

namespace Fundevogel\Thx\Traits;

use Shieldon\SimpleCache\Cache;

use Exception;

trait Caching
{
    /**
     * Properties
     */

    /**
     * Holds tokens of all possible cache drivers
     *
     * See https://github.com/terrylinooo/simple-cache
     *
     * @var array
     */
    private array $cacheDrivers = [
        'file',
        'redis',
        'mongo',
        'mysql',
        'sqlite',
        'apc',
        'apcu',
        'memcache',
        'memcached',
        'wincache',
    ];


    /**
     * Cache driver
     *
     * @var \Shieldon\SimpleCache\Cache
     */
    public Cache $cache;


    /**
     * Defines cache duration (in days)
     *
     * @var int
     */
    public int $cacheDuration = 7;


    /**
     * Methods
     */

    /**
     * Initializes cache instance
     *
     * @param string $cacheDriver Cache driver
     * @param array $cacheSettings Cache settings
     * @return \Shieldon\SimpleCache\Cache
     * @throws Exception
     */
    protected function createCache(string $cacheDriver = 'file', array $cacheSettings = []): Cache
    {
        # Initialize cache
        # (1) Validate provided cache driver
        if (!in_array($cacheDriver, $this->cacheDrivers)) {
            throw new Exception(sprintf('Cache driver "%s" cannot be initiated', $cacheDriver));
        }

        # (2) Merge caching options with defaults
        $cacheSettings = array_merge(['storage'   => './.cache'], $cacheSettings);

        # (2) Create path to caching directory (if not existent) when required by cache driver
        if (in_array($cacheDriver, ['file', 'sqlite'])) {
            $this->createDir($cacheSettings['storage']);
        }

        # (4) Initialize new cache instance
        $cache = new Cache($cacheDriver, $cacheSettings);

        # (5) Build database if using SQLite for the first time
        # TODO: Add check for MySQL, see https://github.com/terrylinooo/simple-cache/issues/8
        if ($cacheDriver === 'sqlite' && !file_exists(join([$cacheSettings['storage'], 'cache.sqlite3']))) {
            $cache->rebuild();
        }

        return $cache;
    }


    /**
     * Creates a new directory
     *
     * @param string $dir The path for the new directory
     * @param bool $recursive Create all parent directories, which don't exist
     * @return bool True: the dir has been created, false: creating failed
     * @throws Exception
     */
    protected function createDir(string $dir, bool $recursive = true): bool
    {
        if (empty($dir) === true) {
            return false;
        }

        if (is_dir($dir) === true) {
            return true;
        }

        $parent = dirname($dir);

        if ($recursive === true) {
            if (is_dir($parent) === false) {
                $this->createDir($parent, true);
            }
        }

        if (is_writable($parent) === false) {
            throw new Exception(sprintf('The directory "%s" cannot be created', $dir));
        }

        return mkdir($dir);
    }
}
