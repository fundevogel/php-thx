<?php

namespace S1SYPHOS\Packaging;


class Packages extends Collection
{
    /**
     * Properties
     */

    /**
     * Processed data
     *
     * @var array
     */
    protected $data;


    /**
     * Constructor
     *
     * @param array $data Processed data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }


    /**
     * Methods
     */

    /**
     * Exports processed package data
     *
     * @return array Processed package data
     */
    public function pkgs(): array
    {
        return $this->data;
    }


    /**
     * Exports package names
     *
     * @return array Package names
     */
    public function packages(): array {
        return $this->pluck($this->data, 'name');
    }


    /**
     * Exports licenses
     *
     * @return array License names
     */
    public function licenses(): array {
        $data = [];

        foreach ($this->data as $pkg) {
            $license = !empty($pkg['license']) ? $pkg['license'] : 'unknown';

            if (!isset($data[$license])) {
                $data[$license] = 0;
            }

            $data[$license]++;
        }

        return $data;
    }


    /**
     * Exports package data sorted by license
     *
     * @return array Package names
     */
    public function byLicense(): array {
        $data = [];

        foreach ($this->data as $pkg) {
            $license = !empty($pkg['license']) ? $pkg['license'] : 'unknown';

            if (!isset($data[$license])) {
                $data[$license] = [];
            }

            $data[$license][] = $pkg;
        }

        return $data;
    }
}
