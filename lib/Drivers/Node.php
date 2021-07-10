<?php

namespace S1SYPHOS\Drivers;


use S1SYPHOS\Driver;


class Node extends Driver
{
    /**
     * Properties
     */

    /**
     * Operating mode identifier
     *
     * @var string
     */
    public $mode = 'npm';


    /**
     * Parses input files
     *
     * @param string $dataFile Path to data file
     * @param string $lockFile Lockfile stream
     * @return array
     */
    protected function load(array $pkgData, string $lockFile): array
    {
        $npmData = [];

        $lockData = json_decode($lockFile, true);

        foreach ($lockData['packages'] as $pkgName => $pkg) {
            if ($this->contains($pkgName, 'node_modules/')) {
                $pkgName = str_replace('node_modules/', '', $pkgName);

                if (in_array($pkgName, array_keys($pkgData['dependencies'])) === true) {
                    $npmData[$pkgName] = $this->ex($pkg);
                }
            }
        }

        return $npmData;
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
