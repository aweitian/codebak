<?php
namespace lib;
class charset
{
	static public function gbk2utf8($str)
	{
		return iconv('gbk', 'utf-8', $str);
	}
	static public function utf82gbk($str)
	{
		return iconv('utf-8', 'gbk', $str);
	}
}