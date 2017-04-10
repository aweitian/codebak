<?php

/**
 * Date: Apr 15, 2016
 * Author: Awei.tian
 * Description: 
 */
namespace lib;

class utility {
	public static function startsWith($haystack, $needle) {
		// search backwards starting from haystack length characters from the end
		return $needle === "" || strrpos ( $haystack, $needle, - strlen ( $haystack ) ) !== false;
	}
	public static function endsWith($haystack, $needle) {
		// search forward starting from end minus needle length characters
		return $needle === "" || (($temp = strlen ( $haystack ) - strlen ( $needle )) >= 0 && strpos ( $haystack, $needle, $temp ) !== false);
	}
	public static function utf8Substr($str, $from, $len) {
		return preg_replace ( '#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $from . '}' . '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $len . '}).*#s', '$1', $str );
	}
	public static function getRandChar($length = 4) {
		$str = "";
		$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
		$max = strlen ( $strPol ) - 1;
		
		for($i = 0; $i < $length; $i ++) {
			$str .= $strPol [rand ( 0, $max )];
		}
		
		return $str;
	}
	static public function win2linux($path) {
		$path = str_replace(':','',$path);
		$path = str_replace('\\','/',$path);
		return '/'.$path;
	}
}