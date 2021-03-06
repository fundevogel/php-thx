<?php

namespace Fundevogel\Thx\Drivers;

use Fundevogel\Thx\Driver;
use Fundevogel\Thx\Packaging\Packages;


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
     * @return \Fundevogel\Thx\Packaging\Packages Processed data
     */
    protected function process(\Shieldon\SimpleCache\Cache $cache, array $config): \Fundevogel\Thx\Packaging\Packages
    {
        $pkgs = array_map(function($pkgName, $pkg) use ($cache, $config) {
            $data = [];

            # Build unique caching key
            $hash = md5($pkgName . $pkg['version']);

            # Fetch information about package ..
            # (1) .. from cache (if available)
            if ($cache->has($hash)) {
                $data = $cache->get($hash);
            }

            # (2) .. from API (if not)
            if (empty($data)) {
                # Block unwanted libraries
                if (in_array($pkgName, $config['blockList']) === true) return false;

                # Prepare data for each repository by determining ..
                # (1) .. name of repository
                $splitList = static::split($pkgName, '/');
                $data['name'] = $splitList[1];

                # (2) .. exact version
                $data['version'] = str_replace('v', '', strtolower($pkg['version']));

                # (3) .. maintainer
                $data['maintainer'] = $splitList[0];

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
