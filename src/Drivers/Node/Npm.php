<?php

declare(strict_types=1);

namespace Fundevogel\Thx\Drivers\Node;

use Fundevogel\Thx\Drivers\Driver;
use Fundevogel\Thx\Utilities\Str;

/**
 * Class Npm
 *
 * Processes 'NodeJS' files
 */
class Npm extends Driver
{
    /**
     * Methods
     */

    /**
     * Extracts raw data from input files
     *
     * @return array
     */
    public function extract(): array
    {
        # Create data buffer
        $data = [];

        # Load lockfile data
        $lockData = json_decode($this->lockFile, true);

        foreach ($lockData['dependencies'] as $pkgName => $pkg) {
            if (isset($this->pkgData['dependencies'][$pkgName])) {
                $data[$pkgName] = $pkg;

                # If version contains package name (= fork) ..
                if (Str::contains($pkg['version'], $pkgName, true)) {
                    # .. lockfile v1 ..
                    if ($lockData['lockfileVersion'] == 1) {
                        # .. clear version
                        $data[$pkgName]['version'] = '';
                    }

                    # .. lockfile v2 ..
                    if ($lockData['lockfileVersion'] == 2) {
                        $key = 'node_modules/' . $pkgName;

                        # .. contains versions even of forked packages
                        if (isset($lockData['packages'][$key])) {
                            $data[$pkgName]['version'] = $lockData['packages'][$key]['version'];
                        }
                    }
                }
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

            # Prepare data for each repository
            $data['name'] = $pkgName;
            $data['version'] = $pkg['version'];

            # Fetch information about package from NPMS API
            $apiURL = 'https://api.npms.io/v2/package/' . rawurlencode($pkgName);

            try {
                $response = $this->fetchRemote($apiURL);
                $response = json_decode($response)->collected->metadata;

                # Split URL & set pointer to last entry
                $repoURL = $response->repository->url;
                $splitList = Str::split($repoURL, '/');
                end($splitList);

                $data['maintainer'] = prev($splitList);
                $data['license'] = $response->license ?? '';
                $data['description'] = $response->description;
                $data['url'] = $response->links->repository ?? $this->formatURL($repoURL);

                # If version is not present (= fork, dev build, ..) ..
                if (empty($data['version'])) {
                    # .. assume current version (v1 only)
                    $data['version'] = $response->version;
                }
            } catch (\Exception $e) {
            }

            return $data;
        }, array_keys($data), $data);

        return $pkgs;
    }


    /**
     * Makes Git repository notation human-readable
     *
     * @param string $url
     * @return string
     */
    protected function formatURL(string $url): string
    {
        # Remove ..
        # (1) .. '.git' from end
        $url = Str::rtrim($url, '.git');

        # (2) .. apostrophes & whitespaces from start
        return Str::ltrim($url, 'git+');
    }
}
