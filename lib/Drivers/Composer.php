<?php

namespace S1SYPHOS\Drivers;

use S1SYPHOS\Driver;
use S1SYPHOS\Packaging\Packages;


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
     * Methods
     */

    /**
     * Extracts raw data from input files
     *
     * @param string $dataFile Path to data file
     * @param string $lockFile Lockfile contents
     * @return array
     */
    protected function extract(array $pkgData, string $lockFile): array
    {
        $lockData = json_decode($lockFile, true);

        $data = [];

        foreach ($lockData['packages'] as $pkg) {
            if (in_array($pkg['name'], array_keys($pkgData['require'])) === true) {
                $data[$pkg['name']] = $pkg;
            }
        }

        return $data;
    }


    /**
     * Processes raw data
     *
     * @param \Shieldon\SimpleCache\Cache $cache Cache object
     * @param array $config Configuration options
     * @return \S1SYPHOS\Packaging\Packages Processed data
     */
    protected function process(\Shieldon\SimpleCache\Cache $cache, array $config): \S1SYPHOS\Packaging\Packages
    {
        $pkgs = array_map(function($pkgName, $pkg) use ($cache, $config) {
            $data = [];

            # Build unique caching key
            $hash = md5($pkgName);

            # Fetch information about package ..
            if ($cache->has($hash)) {
                # (1) .. from cache (if available)
                $data = $cache->get($hash);
            }

            if (empty($data)) {
                # (2) .. from API
                # Block unwanted libraries
                if (in_array($pkgName, $config['blockList']) === true) return false;

                # Prepare data for each repository
                $data['name'] = $pkgName;
                $data['version'] = str_replace('v', '', strtolower($pkg['version']));

                # Fetch additional information from https://packagist.org
                $apiURL = 'https://repo.packagist.org/p/' . $pkgName . '.json';
                $response = $this->fetchRemote($apiURL, $config['timeout'], $config['userAgent']);

                # Skip processing if connection failed
                if (empty($response)) {
                    return $data;
                }

                $response = json_decode($response, true)['packages'][$pkgName];

                # Enrich data with results
                $data['license'] = $response[$pkg['version']]['license'][0] ?? '';
                $data['description'] = $response[$pkg['version']]['description'];
                $data['url'] = static::rtrim($response[$pkg['version']]['source']['url'], '.git');

                # Cache result
                $cache->set($hash, $data, $this->days2seconds($config['cacheDuration']));
            }

            return $data;
        }, array_keys($this->data), $this->data);

        return new Packages($pkgs);
    }
}
