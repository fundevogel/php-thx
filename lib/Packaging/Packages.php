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
    public $data = null;


    /**
     * Constructor
     *
     * @param array $pkgs Processed data
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
}
