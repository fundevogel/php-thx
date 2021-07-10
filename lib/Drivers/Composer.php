<?php

namespace S1SYPHOS\Drivers;


use S1SYPHOS\Driver;


class Composer extends Driver
{
    /**
     * Properties
     */

    /**
     * Operating mode identifier
     *
     * @var string
     */
    public $mode = 'php';


    /**
     * Parses input files
     *
     * @param string $dataFile Path to data file
     * @param string $lockFile Lockfile stream
     * @return array
     */
    protected function load(array $pkgData, string $lockFile): array
    {
        $lockData = json_decode($lockFile, true);

        $phpData = [];

        foreach ($lockData['packages'] as $pkg) {
            if (in_array($pkg['name'], array_keys($pkgData['require'])) === true) {
                $phpData[$pkg['name']] = $this->ex($pkg);
            }
        }

        return $phpData;
    }


    /**
     * Methods
     */

    /**
     * Processes raw data from lockfile
     *
     * @return array Extracted packages
     */
    public function packages(): array
    {
        return [];
    }


    /**
     * Processes raw data from lockfile
     *
     * @param array $array The array to be processed
     * @return string The result array
     */
    protected function ex(array $array): array
    {
        return $array;
    }
}
