<?php

namespace S1SYPHOS;


use S1SYPHOS\Traits\Helpers;


abstract class Driver
{
    /**
     * Traits
     */

    use Helpers;


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
     * Constructor
     *
     * @param string $dataFile Path to data file
     * @param string $lockFile Lockfile stream
     * @return void
     */
    public function __construct(array $pkgData, string $lockFile)
    {
        # Load package data
        # (1) Extract raw data
        $this->data = $this->extract($pkgData, $lockFile);

        # (2) Process raw data
        $this->pkgs = $this->process();
    }


    /**
     * Required methods
     */

    abstract protected function extract(array $pkgData, string $lockFile): array;


    abstract protected function process(): array;


    /**
     * Shared methods
     */

    /**
     * Provides package names
     *
     * @return array Package names
     */
    public function packages(): array {
        return $this->pluck($this->pkgs, 'name');
    }
}
