<?php

namespace S1SYPHOS;


use S1SYPHOS\Traits\Caching;
use S1SYPHOS\Traits\Helpers;
use S1SYPHOS\Traits\Remote;


abstract class Driver
{
    /**
     * Traits
     */

    use Caching;
    use Helpers;
    use Remote;


    /**
     * Properties
     */

    /**
     * Raw data as extracted from lockfile
     *
     * @var array
     */
    public $data = null;


    /**
     * Processed data
     *
     * @var array
     */
    public $pkgs = null;


    /**
     * Operating mode identifier
     *
     * @var string
     */
    public $mode;


    /**
     * List of packages not to be processed
     *
     * @var array
     */
    public $blockList = [];


    /**
     * Constructor
     *
     * @param string $dataFile Path to data file
     * @param string $lockFile Lockfile stream
     * @param string $cacheDriver Cache driver
     * @param array $cacheSettings Cache settings
     * @return void
     */
    public function __construct(array $pkgData, string $lockFile, string $cacheDriver, array $cacheSettings)
    {
        # Create cache instance
        $this->createCache($cacheDriver, $cacheSettings);

        # Extract raw data
        $this->data = $this->extract($pkgData, $lockFile);
    }


    /**
     * Setters & getters
     */

    public function setBlockList(int $blockList): void
    {
        $this->blockList = $blockList;
    }


    public function getBlockList(): array
    {
        return $this->blockList;
    }


    /**
     * Shared methods
     */

    /**
     * Spreads love
     *
     * @return \S1SYPHOS\Driver
     */
    public function spreadLove(): \S1SYPHOS\Driver
    {
        # Process raw data
        $this->pkgs = $this->process();

        # Enable chaining
        return $this;
    }


    /**
     * Exports raw package data
     *
     * @return array Raw package data
     */
    public function data(): array
    {
        return $this->data;
    }


    /**
     * Exports processed package data
     *
     * @return array Processed package data
     */
    public function pkgs(): array
    {
        return $this->pkgs;
    }


    /**
     * Exports package names
     *
     * @return array Package names
     */
    public function packages(): array {
        return $this->pluck($this->pkgs, 'name');
    }


    /**
     * Required methods
     */

    /**
     * Extracts raw data from input files
     *
     * @param string $dataFile Path to data file
     * @param string $lockFile Lockfile contents
     * @return array
     */
    abstract protected function extract(array $pkgData, string $lockFile): array;


    /**
     * Processes raw data
     *
     * @return array Processed data
     */
    abstract protected function process(): array;
}
