<?php

declare(strict_types=1);

namespace Fundevogel\Thx\Drivers\Node;

use Fundevogel\Thx\Utilities\A;
use Fundevogel\Thx\Utilities\Str;

use Spyc;

/**
 * Class Yarn
 *
 * Processes 'Yarnpkg' files
 */
class Yarn extends Npm
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

        # Distinguish versions
        if (Str::contains($this->lockFile, '# yarn lockfile v1')) {
            # Version 1 = not YAML
            $lockData = $this->parseLockFileV1($this->lockFile);

            foreach ($lockData as $pkgName => $pkg) {
                # Determine package name
                $pkgName = $this->getPackageNameV1($pkgName);

                if (isset($this->pkgData['dependencies'][$pkgName])) {
                    $data[$pkgName] = $pkg;
                }
            }
        } else {
            # Version 2 = YAML
            $lockData = $this->parseLockFileV2($this->lockFile);

            foreach ($lockData as $pkgName => $pkg) {
                # Respect packages ..
                # (1) .. from official registry (default)
                if (Str::contains($pkgName, '@npm')) {
                    $pkgName = Str::split($pkgName, '@npm')[0];
                }

                # (2) .. from Git repositories (= forks, dev builds, ..)
                if (Str::contains($pkgName, '@git')) {
                    $pkgName = Str::split($pkgName, '@git')[0];
                }

                if (isset($this->pkgData['dependencies'][$pkgName])) {
                    $data[$pkgName] = $pkg;
                }
            }
        }

        return $data;
    }


    /**
     * Parses v1 lockfile
     *
     * @param string $string The filestream to be parsed
     * @return array Extracted data
     */
    private function parseLockFileV1(string $lockStream): array
    {
        # Prepare data array
        $lockData = [];

        # Initialize base key
        $key = '';

        # Initialize dependency states
        $isDependency = false;
        $isOptionalDependency = false;
        # TODO: Check if that is possible
        $isPeerDependency = false;

        foreach (explode("\n", $lockStream) as $index => $line) {
            # Skip first four lines
            if ($index < 4 || $line === '') {
                continue;
            }

            # Determine nesting level
            $level = Str::length($line) - Str::length(Str::ltrim($line));

            # Normalize line
            $line = $this->cleanString($line);

            # First level:
            # - Reset 'dependency' states
            # - Reset 'optionalDependency' state
            # - Add base array
            if ($level === 0) {
                $isDependency = false;
                $isOptionalDependency = false;
                $isPeerDependency = false;

                if (Str::contains($line, ',')) {
                    $line = Str::split($line, ',')[0];
                }

                $key = $line;
                $lockData[$key] = [];
            }

            # Second level:
            # - Reset 'dependency' states
            # - Add array for 'dependencies'
            # - Add array for 'optionalDependencies'
            # - Add array for 'peerDependencies'
            # - Add key-value pairs for the rest
            if ($level === 2) {
                $isDependency = false;
                $isOptionalDependency = false;
                $isPeerDependency = false;

                if ($line === 'dependencies') {
                    $isDependency = true;
                    $lockData[$key]['dependencies'] = [];

                    # Proceed
                    continue;
                }

                if ($line === 'optionalDependencies') {
                    $isOptionalDependency = true;
                    $lockData[$key]['optionalDependencies'] = [];

                    # Proceed
                    continue;
                }

                if ($line === 'peerDependencies') {
                    $isPeerDependency = true;
                    $lockData[$key]['peerDependencies'] = [];

                    # Proceed
                    continue;
                }

                # Prepare key-value pair
                $list = Str::split($line, ' ');
                $currentKey = $this->cleanString($list[0]);
                $currentValue = $this->cleanString($list[1]);

                $lockData[$key][$currentKey] = $currentValue;
            }

            # Third level:
            # - Add dependency
            # - Add optional dependency
            # - Add peer dependency
            if ($level === 4) {
                if ($isDependency) {
                    $lockData[$key]['dependencies'][] = $line;
                }

                if ($isOptionalDependency) {
                    $lockData[$key]['optionalDependencies'][] = $line;
                }

                if ($isPeerDependency) {
                    $lockData[$key]['peerDependencies'][] = $line;
                }
            }
        }

        return $lockData;
    }


    /**
     * Parses v2 lockfile
     *
     * @param string $string The filestream to be parsed
     * @return array Extracted data
     */
    private function parseLockFileV2(string $lockStream): array
    {
        # Remove BOM
        $string = str_replace("\xEF\xBB\xBF", '', $lockStream);

        # Load YAML data
        return Spyc::YAMLLoadString($string);
    }


    /**
     * Helpers
     */

    /**
     * Determines package name (v1 only)
     *
     * '@my/package@^1.2.3' => '@my/package'
     * 'our/package@^1.2.3' => 'our/package'
     *
     * @param string $pkgName
     * @return string
     */
    private function getPackageNameV1(string $pkgName): string
    {
        # Split along every 'at' symbol
        $array = Str::split($pkgName, '@');

        # Remove last part (= version string)
        array_pop($array);

        # Put remains back together
        return A::join($array, '@');
    }


    /**
     * Removes redundant characters from strings (v1 only)
     *
     * @param string $string
     * @return string
     */
    private function cleanString(string $string): string
    {
        # Remove ..
        # (1) .. apostrophes & colon from end
        $string = Str::rtrim(Str::rtrim($string, ':'), '"');

        # (2) .. apostrophes & whitespaces from start
        return Str::ltrim(Str::ltrim($string), '"');
    }
}
