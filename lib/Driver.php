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
        $this->data = $this->load($pkgData, $lockFile);
    }


    /**
     * Methods
     */

    abstract protected function load(array $pkgData, string $lockFile): array;

    abstract public function packages(): array;
}
