<?php

namespace S1SYPHOS\Traits;


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
}
