<?php
namespace delimiter;
//require_once(dirname(__DIR__)."/../../lib/autoload.php");
class wowgear implements \lib\IDelimiter
{
	public function split($str)
	{
		$data = explode("\n",$str);
		$ret = [];
		$item = "";
		foreach ($data as $v) 
		{
			if(strpos($v,"\t") === false)
			{
				if(!!$item)
				{
					$ret[] = $item;
					$item = "";
				}
				$item = $v."\n---===---\n";
			}
			else
			{
				$item = $item.$v."\n";
			}
		}
		return $ret;
	}
}