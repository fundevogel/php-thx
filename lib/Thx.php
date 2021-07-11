<?php

/**
 * Thx - Acknowledge the people behind your frontend dependencies - and give thanks!
 *
 * @link https://github.com/S1SYPHOS/php-thx
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace S1SYPHOS;

use S1SYPHOS\Exceptions\NoJuiceException;
use S1SYPHOS\Exceptions\NoMannersException;

use S1SYPHOS\Traits\Helpers;
use S1SYPHOS\Traits\Caching;


/**
 * Class Thx
 *
 * Helps to give back & spread the love
 *
 * @package php-thx
 */
class Thx
{
    /**
     * Current version
     */
    const VERSION = '1.1.0';


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
    public $blockList = [];


    /**
     * Current package driver
     *
     * @var \S1SYPHOS\Driver
     */
    public $driver;


    /**
     * Defines timeout for API requests (in seconds)
     *
     * @var int
     */
    protected $timeout = 3;


    /**
     * Controls `User-Agent` header
     *
     * @var string
     */
    protected $userAgent = 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:45.0) Gecko/20100101 Firefox/45.0';


    /**
     * Setters & getters
     */

    public function setBlockList(array $blockList): void
    {
        $this->blockList = $blockList;
    }


    public function getBlockList(): array
    {
        return $this->blockList;
    }


    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }


    public function getTimeout(): string
    {
        return $this->timeout;
    }


    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }


    public function getUserAgent(): string
    {
        return $this->userAgent;
    }


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

        # Select appropriate driver for files
        # (1) Validate lockfile
        $lockFilename = basename($lockFile);

        if (
            !static::contains($lockFilename, 'composer') &&
            !static::contains($lockFilename, 'yarn') &&
            !static::contains($lockFilename, 'package')
        ) {
            throw new NoMannersException(sprintf('Unknown lockfile: "%s".', $lockFilename));
        }

        # (2) Determine package manager by ..
        $lockFile = @file_get_contents($lockFile);

        # .. loading package data
        $pkgData = json_decode(file_get_contents($dataFile), true);

        # .. matching filenames
        $dataFilename = basename($dataFile);

        if ($dataFilename === 'composer.json') {
            if (in_array('require', array_keys($pkgData)) === false) {
                throw new NoJuiceException(sprintf('%s does not contain "require".', $dataFilename));
            }

            $class = 'S1SYPHOS\\Drivers\\Composer';
        }

        if ($dataFilename === 'package.json') {
            if (in_array('dependencies', array_keys($pkgData)) === false) {
                throw new NoJuiceException(sprintf('%s does not contain "dependencies".', $dataFilename));
            }

            # (1) Yarn
            if (static::contains($lockFilename, 'yarn')) {
                $class = 'S1SYPHOS\\Drivers\\Yarn';
            }

            # (2) NPM
            if (static::contains($lockFilename, 'package')) {
                $class = 'S1SYPHOS\\Drivers\\Node';
            }
        }

        # (3) Validate datafile
        if (!isset($class)) {
            throw new NoMannersException(sprintf('Unknown datafile: "%s".', $dataFilename));
        }

        # (4) Instantiate appropriate class
        $this->driver = new $class($pkgData, $lockFile);
    }


    /**
     * Methods
     */

    /**
     * Exports raw data
     *
     * @return array
     */
    public function data(): array
    {
        return $this->driver->data;
    }


    /**
     * Gives back & shares the love
     *
     * @return \S1SYPHOS\Packaging\Packages Processed data
     */
    public function giveBack()
    {
        $config = [
            'bockList' => $this->blockList,
            'cacheDuration' => $this->cacheDuration,
            'timeout' => $this->timeout,
            'userAgent' => $this->userAgent,
        ];

        return $this->driver->spreadLove($this->cache, $config);
    }
}
