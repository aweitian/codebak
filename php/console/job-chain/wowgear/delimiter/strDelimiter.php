<?php
namespace delimiter;
//require_once(dirname(__DIR__)."/../../lib/autoload.php");
class strDelimiter implements \lib\IDelimiter
{
	public function split($str)
	{
		//$delimiter = \lib\console::getArgv("delimiter","---");
		return explode($delimiter, $str);
	}
}