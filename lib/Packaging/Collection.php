<?php

namespace S1SYPHOS\Packaging;

use Countable;
use Iterator;

use S1SYPHOS\Traits\Helpers;


class Collection implements Countable, Iterator
{
    /**
     * Traits
     */

    use Helpers;


    /**
     * Methods
     */

    /**
     * 1) Countable
     */

    /**
     * Counts all objects
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->data);
    }


    /**
     * 2) Iterable
     */

    /**
     * Returns the current object
     *
     * @return \S1SYPHOS\Package
     */
    public function current()
    {
        return current($this->data);
    }


    /**
     * Returns the current key
     *
     * @return string
     */
    public function key()
    {
        return key($this->data);
    }


    /**
     * Moves the cursor to the next object and returns it
     *
     * @return \S1SYPHOS\Package
     */
    public function next()
    {
        return next($this->data);
    }


    /**
     * Moves the cursor to the previous object and returns it
     *
     * @return \S1SYPHOS\Package
     */
    public function prev()
    {
        return prev($this->data);
    }


    /**
     * Moves the cusor to the first object
     *
     * @return void
     */
    public function rewind(): void
    {
        reset($this->data);
    }


    /**
     * Checks if the current object is valid
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->current() !== false;
    }


    /**
     * Exports raw package data
     *
     * @return array Raw package data
     */
    public function data(): array
    {
        return $this->data;
    }
}
