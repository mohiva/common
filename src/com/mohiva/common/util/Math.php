<?php
/**
 * Mohiva Common
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.textile.
 * It is also available through the world-wide-web at this URL:
 * https://github.com/mohiva/common/blob/master/LICENSE.textile
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Util
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\util;

/**
 * A class for arbitrary precision mathematics.
 *
 * PHP must be compiled with --enable-bcmath to use this class.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Util
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class Math {

	/**
	 * Returns the decimal equivalent of a hexadecimal number.
	 *
	 * @param string $hex The hexadecimal number to convert.
	 * @return int|string The decimal equivalent of a hexadecimal number.
	 * @see http://de2.php.net/manual/en/ref.bc.php#99130
	 */
	public static function hexToDec($hex) {

		if(strlen($hex) <= 1) {
			return hexdec($hex);
		} else {
			$remain = substr($hex, 0, -1);
			$last = substr($hex, -1);

			return bcadd(bcmul(16, self::hexToDec($remain)), hexdec($last));
		}
	}

	/**
	 * Returns the hexadecimal equivalent of a decimal number.
	 *
	 * @param string|int $dec The decimal number to convert.
	 * @return string The hexadecimal equivalent of a decimal number.
	 * @see http://de2.php.net/manual/en/ref.bc.php#99130
	 */
	public static function decToHex($dec) {

		$last = bcmod($dec, 16);
		$remain = bcdiv(bcsub($dec, $last), 16);
		if($remain == 0) {
			return dechex($last);
		} else {
			return self::decToHex($remain).dechex($last);
		}
	}
}
