<?php

declare(strict_types=1);

namespace Fundevogel\Thx\Utilities;

class A
{
	/**
	 * Returns the first element of an array
	 *
	 * <code>
	 * $array = [
	 *   'cat'  => 'miao',
	 *   'dog'  => 'wuff',
	 *   'bird' => 'tweet'
	 * ];
	 *
	 * $first = A::first($array);
	 * // first: 'miao'
	 * </code>
	 *
	 * @param array $array The source array
	 * @return mixed The first element
	 */
	public static function first(array $array)
	{
		return array_shift($array);
	}


	/**
	 * Returns the last element of an array
	 *
	 * <code>
	 * $array = [
	 *   'cat'  => 'miao',
	 *   'dog'  => 'wuff',
	 *   'bird' => 'tweet'
	 * ];
	 *
	 * $last = A::last($array);
	 * // last: 'tweet'
	 * </code>
	 *
	 * @param array $array The source array
	 * @return mixed The last element
	 */
	public static function last(array $array)
	{
		return array_pop($array);
	}


	/**
	 * @param mixed $value
	 * @param mixed $separator
	 * @return string
	 */
	public static function join($value, $separator = ', ')
	{
		if (is_string($value) === true) {
			return $value;
		}
		return implode($separator, $value);
	}
}