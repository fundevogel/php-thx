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
            return $this->_process($pkgName, $pkg);
        }, array_keys($this->data), $this->data);
    }


    /**
     * Processes raw data
     *
     * @return array Processed data
     */
    protected function _process(string $pkgName, array $pkg): array
    {
        $data = [];

        # Build unique caching key
        $hash = md5($pkgName);

        # Fetch information about package ..
        if ($this->cache->has($hash)) {
            # (1) .. from cache (if available)
            $data = $this->cache->get($hash);

            $this->fromCache = true;
        }

        if (empty($data)) {
            # (2) .. from API
            # Block unwanted libraries
            if (in_array($pkgName, $this->blockList) === true) return false;

            # Prepare data for each repository
            $data['name'] = $pkgName;
            $data['version'] = $pkg['version'];

            # Fetch additional information from https://api.npms.io
            $response = $this->fetchRemote('https://api.npms.io/v2/package/' . rawurlencode($pkgName));
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
            $this->cache->set($hash, $data, $this->days2seconds($this->cacheDuration));
        }

        return $data;
    }
}
