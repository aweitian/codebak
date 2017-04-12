<?php
namespace app;
class cmd
{
	const RETURN_TYPE_ARRAY = '[]';
	const RETURN_TYPE_RAW = 'V';
	public $cmd;
	public $retType;
	public $argv;
	public $check;
	public function __construct($cmd,$ret = self::RETURN_TYPE_RAW,$argv = [],$check=false)
	{
		$this->cmd = $cmd;
		$this->retType = $ret;
		$this->argv = $argv;
		$this->check = $check;
	}
	static public function make($line)
	{
		$line = trim($line);
		$check = false;
		if(preg_match('/^>\|([^\s]+)/',$line,$m))
		{
			$line = substr(trim($line), strlen($m[1])+3);
			$check = $m[1];
		}
		$arr = explode(" ", $line, 3);
		if(count($arr) == 3)
		{
			$cmd = new self($arr[1],$arr[0],explode(" ",$arr[2]));
		}
		else if(count($arr) == 2)
		{
			$cmd = new self($arr[1],$arr[0]);
		}
		else
		{
			$cmd = new self($arr[0]);
		}
		if($check)
		{
			$cmd->setCheck($check);
		}
		return $cmd;
	}
	public function requireCheck()
	{
		return $this->check !== false;
	}
	public function setCheck($check)
	{
		$this->check = $check;
	}
	public function isArrayReturn()
	{
		return $this->retType == self::RETURN_TYPE_ARRAY;
	}
	public function isRawReturn()
	{
		return $this->retType == self::RETURN_TYPE_RAW;
	}	
}