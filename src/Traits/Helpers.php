<?php

namespace Fundevogel\Thx\Traits;


Trait Helpers
{
    /**
     * Strings
     */

    /**
     * A UTF-8 safe version of strlen()
     *
     * @param string $string
     * @return int
     */
    protected static function length(string $string = null): int
    {
        return mb_strlen($string, 'UTF-8');
    }


    /**
     * Better alternative for explode()
     * It takes care of removing empty values
     * and it has a built-in way to skip values
     * which are too short.
     *
     * @param string $string The string to split
     * @param string $separator The string to split by
     * @param int $length The min length of values.
     * @return array An array of found values
     */
    protected static function split($string, string $separator = ',', int $length = 1): array
    {
        if (is_array($string) === true) {
            return $string;
        }

        $parts  = explode($separator, $string);
        $out    = [];

        foreach ($parts as $p) {
            $p = trim($p);
            if (static::length($p) > 0 && static::length($p) >= $length) {
                $out[] = $p;
            }
        }

        return $out;
    }


    /**
     * Checks if a string contains another string
     *
     * @param string $string
     * @param string $needle
     * @param bool $caseInsensitive
     * @return bool
     */
    protected static function contains(string $string = null, string $needle, bool $caseInsensitive = false): bool
    {
        return call_user_func($caseInsensitive === true ? 'stripos' : 'strpos', $string, $needle) !== false;
    }


    /**
     * Safe rtrim alternative
     *
     * @param string $string
     * @param string $trim
     * @return string
     */
    public static function rtrim(string $string, string $trim = ' '): string
    {
        return preg_replace('!(' . preg_quote($trim) . ')+$!', '', $string);
    }


    /**
     * Arrays
     */

    /**
     * Plucks a single column from an array
     *
     * @param array $array The source array
     * @param string $key The key name of the column to extract
     * @return array The result array with all values from that column.
     */
    protected static function pluck(array $array, string $key): array
    {
        $output = [];

        foreach ($array as $a) {
            if (isset($a[$key]) === true) {
                $output[] = $a[$key];
            }
        }

        return $output;
    }


    /**
     * Sorts a multi-dimensional array by a certain column
     *
     * @param array $array The source array
     * @param string $field The name of the column
     * @param string $direction desc (descending) or asc (ascending)
     * @param int $method A PHP sort method flag or 'natural' for natural sorting, which is not supported in PHP by sort flags
     * @return array The sorted array
     */
    protected static function sort(array $array, string $field, string $direction = 'desc', $method = SORT_REGULAR): array
    {
        $direction = strtolower($direction) === 'desc' ? SORT_DESC : SORT_ASC;
        $helper    = [];
        $result    = [];

        // build the helper array
        foreach ($array as $key => $row) {
            $helper[$key] = $row[$field];
        }

        // natural sorting
        if ($direction === SORT_DESC) {
            arsort($helper, $method);
        } else {
            asort($helper, $method);
        }

        // rebuild the original array
        foreach ($helper as $key => $val) {
            $result[$key] = $array[$key];
        }

        return $result;
    }


    /**
     * Miscellaneous
     */

    /**
     * Converts days to seconds
     *
     * @param int $days
     * @return int
     */
    protected function days2seconds(int $days): int
    {
        return $days * 24 * 60 * 60;
    }
}
