<?php

/**
 * Thx - Acknowledge the people behind your frontend dependencies - and give thanks!
 *
 * @link https://github.com/S1SYPHOS/php-thx
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace S1SYPHOS;


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
    const VERSION = '0.2.0';


    /**
     * Traits
     */

    use \S1SYPHOS\Traits\Helpers;


    /**
     * Methods
     */

    /**
     * Gives back & shows some love
     *
     * @param string $dataFile Datafile, eg 'composer.json' or 'package.json'
     * @param string $lockFile Lockfile, eg 'composer.lock', 'package-lock.json' or 'yarn.lock'
     * @return \S1SYPHOS\Driver
     */
    public static function giveBack(string $dataFile, string $lockFile, string $cacheDriver = 'file', array $cacheSettings = [])
    {
        # Validate lockfile
        $lockFilename = basename($lockFile);

        if (
            !static::contains($lockFilename, 'composer') &&
            !static::contains($lockFilename, 'yarn') &&
            !static::contains($lockFilename, 'package')
        ) {
            throw new \Exception(sprintf('Unknown lockfile: "%s".', $lockFilename));
        }

        # Determine package manager
        $lockFile = @file_get_contents($lockFile);

        # Load package data
        $pkgData = json_decode(file_get_contents($dataFile), true);

        $dataFilename = basename($dataFile);

        if ($dataFilename === 'composer.json') {
            if (in_array('require', array_keys($pkgData)) === false) {
                throw new \Exception(sprintf('%s does not contain "require".', $dataFilename));
            }

            $class = 'S1SYPHOS\\Drivers\\Composer';
        }

        if ($dataFilename === 'package.json') {
            if (in_array('dependencies', array_keys($pkgData)) === false) {
                throw new \Exception(sprintf('%s does not contain "dependencies".', $dataFilename));
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

        if (!isset($class)) {
            throw new \Exception(sprintf('Unknown datafile: "%s".', $dataFilename));
        }

        return new $class($pkgData, $lockFile, $cacheDriver, $cacheSettings);
    }
}
