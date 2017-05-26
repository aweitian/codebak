<?php
namespace Tian;
//把data/result.txt格式转化为数组
//转化后的格式为:
/**
[
	title => [
		score1
		score2
	]
	...
]
*/
class Result2arr
{
	private $data = array();
	private $path;
	private $lq = '';
	private static $inst;
	public function __construct($f="conf/result.txt")
	{
		$this->path = $f;
		$this->init();
	}
	public function init()
	{
		$d = file($this->path);
		foreach ($d as $line) 
		{
			if(\Tian\Utility::startsWith($line,'//'))
				continue;
			//不存在（26分-29分）
			if(preg_match("/^(.+?)(?:\(|（)(\d+)\s*分?\s*-\s*(\d+)\s*分?\s*(?:）|\))\s*$/",$line,$m))
			{
				$this->data[$m[1]] = array($m[2],$m[3]);
			}
		}
	}
	public function get()
	{
		return $this->data;
	}
}