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
     * List of packages not to be processed
     *
     * @var array
     */
    public $blockList = [
        'php',
    ];


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

        $phpData = [];

        foreach ($lockData['packages'] as $pkg) {
            if (in_array($pkg['name'], array_keys($pkgData['require'])) === true) {
                $phpData[$pkg['name']] = $pkg;
            }
        }

        return $phpData;
    }


    /**
     * Processes raw data
     *
     * @return array Processed data
     */
    protected function process(): array
    {
        return array_map(function($pkgName, $pkg) {
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
                $data['version'] = str_replace('v', '', strtolower($pkg['version']));

                # Fetch additional information from https://packagist.org
                $response = $this->fetchRemote('https://repo.packagist.org/p/' . $pkgName . '.json');
                $response = json_decode($response, true)['packages'][$pkgName];

                # Enrich data with results
                $data['license'] = $response[$pkg['version']]['license'][0] ?? '';
                $data['description'] = $response[$pkg['version']]['description'];
                $data['url'] = static::rtrim($response[$pkg['version']]['source']['url'], '.git');

                # Cache result
                $this->cache->set($hash, $data, $this->days2seconds($this->cacheDuration));
            }

            return $data;
        }, array_keys($this->data), $this->data);
    }
}
