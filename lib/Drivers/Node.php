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
     * Extracts raw data from input files
     *
     * @param string $dataFile Path to data file
     * @param string $lockFile Lockfile contents
     * @return array
     */
    protected function extract(array $pkgData, string $lockFile): array
    {
        $npmData = [];

        $lockData = json_decode($lockFile, true);

        foreach ($lockData['packages'] as $pkgName => $pkg) {
            if ($this->contains($pkgName, 'node_modules/')) {
                $pkgName = str_replace('node_modules/', '', $pkgName);

                if (in_array($pkgName, array_keys($pkgData['dependencies'])) === true) {
                    $npmData[$pkgName] = $pkg;
                }
            }
        }

        return $npmData;
    }


    /**
     * Processes raw data
     *
     * @return array Processed data
     */
    protected function process(): array
    {
        return array_map(function($pkgName, $pkg) {
            return [
                'name' => $pkgName,
                'version' => $pkg['version'],
            ];
        }, array_keys($this->data), $this->data);
    }
}
