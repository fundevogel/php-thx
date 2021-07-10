<?php

/**
 * Thx - Acknowledge the people behind your frontend dependencies - and give thanks!
 *
 * @link https://github.com/S1SYPHOS/php-thx
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace S1SYPHOS;


use S1SYPHOS\Drivers\Composer;
use S1SYPHOS\Drivers\Node;
use S1SYPHOS\Drivers\Yarn;

use S1SYPHOS\Traits\Helpers;


/**
 * Class Thx
 *
 * Provides relevant data to show some love
 *
 * @package php-thx
 */
class Thx
{
    /**
     * Current version
     */
    const VERSION = '0.1.0';


    /**
     * Traits
     */

    use Helpers;


    /**
     * Properties
     */

    /**
     * Selected driver
     *
     * @var \S1SYPHOS\Driver
     */
    public $driver = null;


    /**
     * Constructor
     *
     * @param string $dataFile Datafile, eg 'composer.json' or 'package.json'
     * @param string $lockFile Lockfile, eg 'composer.lock', 'package-lock.json' or 'yarn.lock'
     * @return void
     */
    public function __construct(string $dataFile, string $lockFile)
    {
        # Validate lockfile
        $lockFilename = basename($lockFile);

        if (
            !$this->contains($lockFilename, 'composer') &&
            !$this->contains($lockFilename, 'yarn') &&
            !$this->contains($lockFilename, 'package')
        ) {
            throw new \Exception(sprintf('Lockfile "%s" could not be recognized.', $lockFilename));
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

            $this->driver = new Composer($pkgData, $lockFile);
        }

        if ($dataFilename === 'package.json') {
            if (in_array('dependencies', array_keys($pkgData)) === false) {
                throw new \Exception(sprintf('%s does not contain "dependencies".', $dataFilename));
            }

            # (1) Yarn
            if ($this->contains($lockFilename, 'yarn')) {
                $this->driver = new Yarn($pkgData, $lockFile);
            }

            # (2) NPM
            if ($this->contains($lockFilename, 'package')) {
                $this->driver = new Node($pkgData, $lockFile);
            }
        }
    }


    /**
     * Methods
     */

    public function data(): array
    {
        return $this->driver->data;
    }


    public function pkgs(): array
    {
        return $this->driver->pkgs;
    }


    public function packages(): array
    {
        return $this->driver->packages();
    }
}
