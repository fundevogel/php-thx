<?php

namespace S1SYPHOS\Drivers;


use S1SYPHOS\Driver;


class Yarn extends Driver
{
    /**
     * Properties
     */

    /**
     * Operating mode identifier
     *
     * @var string
     */
    public $mode = null;


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

        # Distinguish versions
        $v1 = '# yarn lockfile v1';

        if ($this->contains($lockFile, $v1)) {
            # Version 1 = not YAML
            $this->mode = 'yarn-v1';

            $lockData = $this->parseLockFile($lockFile);

            foreach ($lockData as $pkgName => $pkg) {
                $pkgName = substr($pkgName, 0, strpos($pkgName, '@'));

                if (in_array($pkgName, array_keys($pkgData['dependencies'])) === true) {
                    $npmData[$pkgName] = $pkg;
                }
            }

        } else {
            # Version 2 = YAML
            $this->mode = 'yarn-v2';

            $lockData = yaml_parse($lockFile);

            foreach ($lockData as $pkgName => $pkg) {
                if ($this->contains($pkgName, '@npm')) {
                    $pkgName = $this->split($pkgName, '@npm')[0];

                    if (in_array($pkgName, array_keys($pkgData['dependencies'])) === true) {
                        $npmData[$pkgName] = $pkg;
                    }
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
            return [
                'name' => $pkgName,
                'version' => $pkg['version'],
            ];
        }, array_keys($this->data), $this->data);
    }


    /**
     * Methods
     */

    /**
     * Removes redundant characters from strings
     *
     * @param string $string The string to be normalized
     * @return string The result string
     */
    protected function normalize(string $string): string
    {
        # From end of string, remove
        # - colon
        # - apostrophes
        $string = rtrim(rtrim($string, ':'), '"');

        # From start of string, remove
        # - whitespaces
        # - apostrophes
        return ltrim(ltrim($string), '"');
    }


    /**
     * Parses v1 yarn lockfile
     *
     * @param string $string The filestream to be parsed
     * @return string The result array representing its content
     */
    protected function parseLockFile($lockStream): array
    {
        # Prepare data array
        $lockData = [];

        # Initialize base key
        $key = null;

        # Initialize dependency states
        $isDependency = false;
        $isOptionalDependency = false;
        # TODO: Check if that is possible
        $isPeerDependency = false;

        foreach (explode("\n", $lockStream) as $index => $line) {
            # Skip first four lines
            if ($index < 4 || $line === '') continue;

            # Determine nesting level
            $level = strlen($line) - strlen(ltrim($line));

            # Normalize line
            $line = $this->normalize($line);

            # First level:
            # - Reset 'dependency' states
            # - Reset 'optionalDependency' state
            # - Add base array
            if ($level === 0) {
                $isDependency = false;
                $isOptionalDependency = false;
                $isPeerDependency = false;

                if ($this->contains($line, ',')) {
                    $line = $this->split($line, ',')[0];
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
                $list = $this->split($line, ' ');
                $currentKey = $list[0];
                $currentValue = $this->normalize($list[1]);

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
}
