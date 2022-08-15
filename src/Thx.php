<?php

declare(strict_types=1);

/**
 * Thx - Acknowledge the people behind your frontend dependencies - and give thanks!
 *
 * @link https://codeberg.org/Fundevogel/php-thx
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Fundevogel\Thx;

use Fundevogel\Thx\Traits\Helpers;
use Fundevogel\Thx\Traits\Caching;

/**
 * Class Thx
 *
 * Helps to give back & spread the love
 *
 * @package php-thx
 */
final class Thx
{
    /**
     * Traits
     */

    use Caching;
    use Helpers;


    /**
     * List of packages not to be processed
     *
     * @var array
     */
    public array $blockList = [];


    /**
     * Current package driver
     *
     * @var \Fundevogel\Thx\Driver
     */
    public Driver $driver;


    /**
     * Defines timeout for API requests (in seconds)
     *
     * @var int
     */
    public int $timeout = 3;


    /**
     * Controls `User-Agent` header
     *
     * @var string
     */
    public string $userAgent = 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:45.0) Gecko/20100101 Firefox/45.0';


    /**
     * Constructor
     *
     * @param string $dataFile Path to datafile, eg 'composer.json' or 'package.json'
     * @param string $lockFile Path tp lockfile, eg 'composer.lock', 'package-lock.json' or 'yarn.lock'
     * @param string $cacheDriver Cache driver
     * @param array $cacheSettings Cache settings
     * @return void
     */
    public function __construct(string $dataFile, string $lockFile, string $cacheDriver = 'file', array $cacheSettings = [])
    {
        # Create cache instance
        $this->cache = $this->createCache($cacheDriver, $cacheSettings);

        # Instantiate driver
        $this->driver = Factory::create($dataFile, $lockFile);
    }


    /**
     * Methods
     */

    /**
     * Gives back & shares the love
     *
     * @return \Fundevogel\Thx\Packaging\Packages Processed data
     */
    public function giveBack()
    {
        $config = [
            'blockList' => $this->blockList,
            'cacheDuration' => $this->cacheDuration,
            'timeout' => $this->timeout,
            'userAgent' => $this->userAgent,
        ];

        return $this->driver->spreadLove($this->cache, $config);
    }
}
