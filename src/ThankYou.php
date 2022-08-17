<?php

declare(strict_types=1);

/**
 * Thx - Acknowledge the people behind your frontend dependencies - and give thanks!
 *
 * @link https://codeberg.org/Fundevogel/php-thx
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Fundevogel\Thx;

use Fundevogel\Thx\Drivers\Driver;
use Fundevogel\Thx\Drivers\Node\Npm;
use Fundevogel\Thx\Drivers\Node\Yarn;
use Fundevogel\Thx\Drivers\Php\Composer;
use Fundevogel\Thx\Utilities\Str;

/**
 * Class ThankYou
 *
 * Helps to give back & spread the love
 */
final class ThankYou
{
    /**
     * Methods
     */

    /**
     * Gives back & spread the love
     *
     * @param string $dataFile Path to datafile
     * @param string $lockFile Path tp lockfile
     * @return array
     */
    public static function veryMuch(string $dataFile, string $lockFile): array
    {
        # Instantiate driver
        $driver = static::haveFun($dataFile, $lockFile);

        # Enjoy your ride
        return $driver->spreadLove();
    }


    /**
     * Enjoy the party & have some fun
     *
     * @param string $dataFile Path to datafile
     * @param string $lockFile Path tp lockfile
     * @return \Fundevogel\Thx\Drivers\Driver
     * @throws \Exception Unknown or invalid file(s)
     */
    public static function haveFun(string $dataFile, string $lockFile): Driver
    {
        # Load package data
        $pkgData = json_decode(file_get_contents($dataFile), true);

        # Load lockfile data
        $lockData = @file_get_contents($lockFile);

        # Match filename of datafile
        $dataFilename = basename($dataFile);

        # (1) Composer
        if ($dataFilename == 'composer.json') {
            if (!isset($pkgData['require'])) {
                throw new \Exception(sprintf('%s does not contain "require".', $dataFilename));
            }

            # Load driver
            return new Composer($pkgData, $lockData);
        }

        # (2) Node
        if ($dataFilename == 'package.json') {
            if (!isset($pkgData['dependencies'])) {
                throw new \Exception(sprintf('%s does not contain "dependencies".', $dataFilename));
            }

            # Get lockfile name
            $lockFilename = basename($lockFile);

            # (a) NPM
            if (Str::contains($lockFilename, 'package')) {
                return new Npm($pkgData, $lockData);
            }

            # (b) Yarn
            if (Str::contains($lockFilename, 'yarn')) {
                return new Yarn($pkgData, $lockData);
            }

            throw new \Exception(sprintf('Unknown lockfile: "%s".', $lockFilename));
        }

        throw new \Exception(sprintf('Unknown datafile: "%s".', $dataFilename));
    }
}
