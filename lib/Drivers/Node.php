<?php

namespace Fundevogel\Thx\Drivers;


use Fundevogel\Thx\Driver;
use Fundevogel\Thx\Packaging\Packages;


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
     * @return \Fundevogel\Thx\Packaging\Packages Processed data
     */
    protected function process(\Shieldon\SimpleCache\Cache $cache, array $config): \Fundevogel\Thx\Packaging\Packages
    {
        $pkgs = array_map(function($pkgName, $pkg) use ($cache, $config) {
            $data = [];

            # Build unique caching key
            $hash = md5($pkgName . $pkg['version']);

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

                # Skip processing if connection is faulty
                if (empty($response)) {
                    return $data;
                }

                $response = json_decode($response)->collected->metadata;

                # Split URL & set pointer to last entry
                $repoURL = $response->links->repository;

                $splitList = static::split($repoURL, '/');
                end($splitList);

                $data['maintainer'] = prev($splitList);
                $data['license'] = $response->license ?? '';
                $data['description'] = $response->description;
                $data['url'] = $repoURL;

                # Cache result
                $cache->set($hash, $data, $this->days2seconds($config['cacheDuration']));
            }

            return $data;
        }, array_keys($this->data), $this->data);

        return new Packages($pkgs);
    }
}
