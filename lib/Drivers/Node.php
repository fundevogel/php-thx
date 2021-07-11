<?php

namespace S1SYPHOS\Drivers;


use S1SYPHOS\Driver;
use S1SYPHOS\Packaging\Packages;


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
        $data = [];

        $lockData = json_decode($lockFile, true);

        foreach ($lockData['packages'] as $pkgName => $pkg) {
            if ($this->contains($pkgName, 'node_modules/')) {
                $pkgName = str_replace('node_modules/', '', $pkgName);

                if (in_array($pkgName, array_keys($pkgData['dependencies'])) === true) {
                    $data[$pkgName] = $pkg;
                }
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
                $data['version'] = $pkg['version'];

                # Fetch additional information from https://api.npms.io
                $apiURL = 'https://api.npms.io/v2/package/' . rawurlencode($pkgName);
                $response = $this->fetchRemote($apiURL, $config['timeout'], $config['userAgent']);
                $response = json_decode($response)->collected->metadata;

                $data['license'] = $response->license ?? '';
                $data['description'] = $response->description;
                $data['url'] = $response->links->repository;
                $data['forked'] = false;

                # Check if it's a forked repository
                if (preg_match('/(([0-9])+(\.{0,1}([0-9]))*)/', $data['version']) == false) {
                    # TODO: Check if that's even a thing
                    # $data['version'] = $data->version;
                    $data['forked'] = true;
                }

                # Cache result
                $cache->set($hash, $data, $this->days2seconds($config['cacheDuration']));
            }

            return $data;
        }, array_keys($this->data), $this->data);

        return new Packages($pkgs);
    }
}
