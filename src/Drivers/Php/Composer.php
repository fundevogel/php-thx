<?php

declare(strict_types=1);

namespace Fundevogel\Thx\Drivers\Php;

use Fundevogel\Thx\Drivers\Driver;
use Fundevogel\Thx\Utilities\Str;

/**
 * Class Composer
 *
 * Processes 'Composer' files
 */
class Composer extends Driver
{
    /**
     * Methods
     */

    /**
     * Extracts raw data from input files
     *
     * @return array
     */
    protected function extract(): array
    {
        # Create data array
        $data = [];

        # Load lockfile data
        $lockData = json_decode($this->lockFile, true);

        foreach ($lockData['packages'] as $pkg) {
            if (isset($this->pkgData['require'][$pkg['name']])) {
                $data[$pkg['name']] = $pkg;
            }
        }

        return $data;
    }


    /**
     * Retrieves additional package information
     *
     * @param array $data Extracted data
     * @return array Processed data
     */
    protected function extend(array $data): array
    {
        $pkgs = array_map(function (string $pkgName, array $pkg) {
            # Create data array
            $data = [];

            # Prepare data for each repository by determining ..
            # (1) .. name of repository
            $list = Str::split($pkgName, '/');
            $data['name'] = $list[1];

            # (2) .. exact version
            $data['version'] = str_replace('v', '', strtolower($pkg['version']));

            # (3) .. maintainer
            $data['maintainer'] = $list[0];

            # Fetch information about package from Packagist API
            $apiURL = 'https://repo.packagist.org/p/' . $pkgName . '.json';

            try {
                $response = $this->fetchRemote($apiURL);
                $response = json_decode($response, true)['packages'][$pkgName];

                # Enrich data with results
                $data['license'] = $response[$pkg['version']]['license'][0] ?? '';
                $data['description'] = $response[$pkg['version']]['description'];
                $data['url'] = Str::rtrim($response[$pkg['version']]['source']['url'], '.git');
            } catch (\Exception $e) {
            }

            return $data;
        }, array_keys($data), $data);

        return $pkgs;
    }
}
