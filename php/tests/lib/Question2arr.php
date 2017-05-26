<?php
namespace Tian;
//把data/question.txt格式转化为数组
//转化后的格式为:
/**
[
	'question' => [
		[score,option]
		...
	]
	...
]


*/
class Question2arr
{
	private $data = array();
	private $path;
	private $lq = '';
	private static $inst;
	public function __construct($f="conf/question.txt")
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
			if(preg_match("/^[^\t]/",$line))
			{
				$this->lq = trim($line);
				if($this->lq)
					$this->data[$this->lq] = array();
			}
			else
			{
				if (!$this->lq) 
					continue;
				$this->data[$this->lq][] = $this->parse(trim($line));
			}

		}
	}
	//处理选项分数,返回数组
	private function parse($v)
	{
		//如果以数字结尾，以这个数字为准
		if(preg_match("/(\d+)$/",$v,$m))
		{
			$s = $m[1];
			$c = trim(substr($v,0,-1 * strlen($s)));
		}
		else
		{
			$s = count($this->data[$this->lq]) + 1;
			$c = $v;
		}
		return array($s,$c);
	}
	public function get()
	{
		return $this->data;
	}
}