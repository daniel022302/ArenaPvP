<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 3/1/2017
 * Time: 6:45 PM
 */

namespace sys\arenapvp\utils;

use pocketmine\utils\TextFormat;

class Utils {

	/**
	 * Removes all coloring and color codes from a string
	 *
	 * @param $string
	 *
	 * @return mixed
	 */
	public static function cleanString($string) {
		$string = self::translateColors($string);
		$string = TextFormat::clean($string);

		return $string;
	}

	/**
	 * Apply Minecraft color codes to a string from our custom ones
	 *
	 * @param string $string
	 * @param string $symbol
	 *
	 * @return mixed
	 */
	public static function translateColors($string, $symbol = "&") {
		return str_replace($symbol, TextFormat::ESCAPE, $string);
	}

	/**
	 * Replaces all in a string spaces with -
	 *
	 * @param $string
	 *
	 * @return mixed
	 */
	public static function stripSpaces($string) {
		return str_replace(" ", "_", $string);
	}

	/**
	 * Strip all white space in a string
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function stripWhiteSpace(string $string) {
		$string = preg_replace('/\s+/', "", $string);
		$string = preg_replace('/=+/', '=', $string);

		return $string;
	}

	/**
	 * Center a line of text based around the length of another line
	 *
	 * @param $toCenter
	 * @param $checkAgainst
	 *
	 * @return string
	 */
	public static function centerText($toCenter, $checkAgainst) {
		if (strlen($toCenter) >= strlen($checkAgainst)) {
			return $toCenter;
		}

		$times = floor((strlen($checkAgainst) - strlen($toCenter)) / 2);

		return str_repeat(" ", ($times > 0 ? $times : 0)) . $toCenter;
	}

	/**
	 * Return the stack trace
	 *
	 * @param int $start
	 * @param null $trace
	 *
	 * @return array
	 */
	public static function getTrace($start = 1, $trace = null) {
		if ($trace === null) {
			if (function_exists("xdebug_get_function_stack")) {
				$trace = array_reverse(xdebug_get_function_stack());
			} else {
				$e = new \Exception();
				$trace = $e->getTrace();
			}
		}
		$messages = [];
		$j = 0;
		for ($i = (int)$start; isset($trace[$i]); ++$i, ++$j) {
			$params = "";
			if (isset($trace[$i]["args"]) or isset($trace[$i]["params"])) {
				if (isset($trace[$i]["args"])) {
					$args = $trace[$i]["args"];
				} else {
					$args = $trace[$i]["params"];
				}
				foreach ($args as $name => $value) {
					$params .= (is_object($value) ? get_class($value) . " " . (method_exists($value, "__toString") ? $value->__toString() : "object") : gettype($value) . " " . @strval($value)) . ", ";
				}
			}
			$messages[] = "#$j " . (isset($trace[$i]["file"]) ? ($trace[$i]["file"]) : "") . "(" . (isset($trace[$i]["line"]) ? $trace[$i]["line"] : "") . "): " . (isset($trace[$i]["class"]) ? $trace[$i]["class"] . (($trace[$i]["type"] === "dynamic" or $trace[$i]["type"] === "->") ? "->" : "::") : "") . $trace[$i]["function"] . "(" . substr($params, 0, -2) . ")";
		}

		return $messages;
	}

	/**
	 * Uses SHA-512 [http://en.wikipedia.org/wiki/SHA-2] and Whirlpool
	 * [http://en.wikipedia.org/wiki/Whirlpool_(cryptography)]
	 *
	 * Both of them have an output of 512 bits. Even if one of them is broken in the future, you have to break both
	 * of them at the same time due to being hashed separately and then XORed to mix their results equally.
	 *
	 * @param string $salt
	 * @param string $password
	 *
	 * @return string[128] hex 512-bit hash
	 */
	public static function hash($salt, $password) {
		$salt = strtolower($salt);
		return bin2hex(hash("sha512", $password . $salt, true) ^ hash("whirlpool", $salt . $password, true));
	}

}