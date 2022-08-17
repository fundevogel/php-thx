<?php

declare(strict_types=1);

namespace Fundevogel\Thx\Drivers\Node;

use Fundevogel\Thx\Utilities\A;
use Fundevogel\Thx\Utilities\Str;

/**
 * Class Pnpm
 *
 * Processes 'pnpm' files
 */
class Pnpm extends Npm
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
        # Create data array
        $data = [];

        # Version 2 = YAML
        $lockData = $this->parseYaml($this->lockFile);

        foreach ($lockData['dependencies'] as $pkgName => $version) {
            # Respect packages from Git repositories (= forks, dev builds, ..)
            if (Str::contains($version, $pkgName)) {
                # Reset version
                $version = '';

                # Iterate over all installed packages
                foreach ($lockData['packages'] as $pkgString => $pkg) {
                    # If one of them contains current package ..
                    if (isset($pkg['name']) && $pkg['name'] == $pkgName) {
                        # (1) .. get installed version
                        $version = $pkg['version'];

                        # (2) .. abort execution
                        break;
                    }
                }
            }

            if (isset($this->pkgData['dependencies'][$pkgName])) {
                $data[$pkgName] = ['version' => $version];
            }
        }

        return $data;
    }
}
