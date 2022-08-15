<?php

declare(strict_types=1);

namespace Fundevogel\Thx;

use Fundevogel\Thx\Drivers\Composer;
use Fundevogel\Thx\Drivers\Npm;
use Fundevogel\Thx\Drivers\Yarn;
use Fundevogel\Thx\Traits\Helpers;

use Exception;

/**
 * Class Factory
 */
class Factory
{
    /**
     * Traits
     */

    use Helpers;


    /**
     * Instantiates appropriate driver
     *
     * @param string $dataFile Path to datafile, eg 'composer.json' or 'package.json'
     * @param string $lockFile Path tp lockfile, eg 'composer.lock', 'package-lock.json' or 'yarn.lock'
     * @return \Fundevogel\Thx\Driver
     * @throws Exception
     */
    public static function create(string $dataFile, string $lockFile): Driver
    {
        # Get lockfile name
        $lockFilename = basename($lockFile);

        # Determine package manager by ..
        $lockFile = @file_get_contents($lockFile);

        # .. loading package data
        $pkgData = json_decode(file_get_contents($dataFile), true);

        # .. matching filenames
        $dataFilename = basename($dataFile);

        switch ($dataFilename) {
            case 'composer.json':
                if (!isset($pkgData['require'])) {
                    throw new Exception(sprintf('%s does not contain "require".', $dataFilename));
                }

                return new Composer($pkgData, $lockFile);

            case 'package.json':
                if (!isset($pkgData['dependencies'])) {
                    throw new Exception(sprintf('%s does not contain "dependencies".', $dataFilename));
                }

                # (1) NPM
                if (static::contains($lockFilename, 'package')) {
                    return new Npm($pkgData, $lockFile);
                }

                # (2) Yarn
                if (static::contains($lockFilename, 'yarn')) {
                    return new Yarn($pkgData, $lockFile);
                }

                throw new Exception(sprintf('Unknown lockfile: "%s".', $lockFilename));
        }

        throw new Exception(sprintf('Unknown datafile: "%s".', $dataFilename));
    }
}
