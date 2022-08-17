<?php

declare(strict_types=1);

namespace Fundevogel\Thx\Utilities;

class Str
{
	/**
	 * A UTF-8 safe version of strlen()
	 *
	 * @param string $string
	 * @return int
	 */
	public static function length(string $string = null): int
	{
		return mb_strlen($string ?? '', 'UTF-8');
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
	public static function split($string, string $separator = ',', int $length = 1): array
	{
		if (is_array($string) === true) {
			return $string;
		}

		// make sure $string is string
		$string ??= '';

		$parts = explode($separator, $string);
		$out   = [];

		foreach ($parts as $p) {
			$p = trim($p);
			if (static::length($p) > 0 && static::length($p) >= $length) {
				$out[] = $p;
			}
		}

		return $out;
	}


	/**
	 * Checks if a str contains another string
	 *
	 * @param string $string
	 * @param string $needle
	 * @param bool $caseInsensitive
	 * @return bool
	 */
	public static function contains(string $string = null, string $needle, bool $caseInsensitive = false): bool
	{
		if ($needle === '') {
			return true;
		}

		$method = $caseInsensitive === true ? 'stripos' : 'strpos';
		return call_user_func($method, $string ?? '', $needle) !== false;
	}


	/**
	 * Safe ltrim alternative
	 *
	 * @param string $string
	 * @param string $trim
	 * @return string
	 */
	public static function ltrim(string $string, string $trim = ' '): string
	{
		return preg_replace('!^(' . preg_quote($trim) . ')+!', '', $string);
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
}
