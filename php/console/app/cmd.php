<?php
//conf/alias文件为CMD的别名文件，格式为([]|-:)alias balabala....
//其中括号中为可选返回类型,返回类型优先级为命令中自带最高，别名返回类型次之，最后为默认返回
//命令格式为
//		>|filter [delimiter] []:alias/cmd args
namespace app;
class cmd
{
	const RETURN_TYPE_ARRAY = '[]';
	const RETURN_TYPE_RAW = '-';
	const RETURN_TYPE_DEFAULT = '-';
	public $cmd;
	public $retType = '-';
	public $argv;
	public $check;
	public $defChk = true;
	public $delimiter = "\n";
	public function __construct($cmd,$argv = [])
	{
		$this->setDefChk();
		$this->aliasCmd($cmd);
		$this->argv = $argv;
	}
	public function getCmd()
	{
		return $this->cmd . ' ' . join(' ',$this->argv);
	}
	private function setDefChk()
	{
		$this->check = "emptychk";
	}
	private function aliasCmd($cmd)
	{
		$cmd = trim($cmd);
		//分析是否带返回类型
		if(preg_match("/^\[(\w*)\]:/",$cmd,$m))
		{
			$cmd = substr($cmd, strlen($m[0]));
			$this->delimiter = $m[1];
			$this->alias($cmd);
			$this->setReturnType(self::RETURN_TYPE_ARRAY);
		}
		else if(\lib\utility::startsWith($cmd,"-:"))
		{
			$cmd = substr($cmd, 2);
			$this->alias($cmd);
			$this->setReturnType(self::RETURN_TYPE_RAW);
		}
		else
		{
			$this->alias($cmd);
		}
	}
	private function alias($alias)
	{
		foreach (file(__DIR__.'/../conf/alias') as $line) 
		{
			$line = trim($line);
			$tmp = explode(" ",$line,2);
			$f_type = false;

			$f_delimiter = false;
			if(count($tmp) == 2)
			{
				$cmd = $tmp[0];

				if(preg_match("/^\[(\w*)\]:/",$cmd,$m))
				{				
					$cmd = substr($cmd, strlen($m[0]));
					if(!!$m[1])
					{
						$delimiter = $m[1];
						$f_delimiter = true;
					}
					$type = self::RETURN_TYPE_ARRAY;
					$f_type = true;
				}
				else if(\lib\utility::startsWith($cmd,"-:"))
				{
					$cmd = substr($cmd, 2);
					$type = self::RETURN_TYPE_RAW;
					$f_type = true;
				}
				
				if($alias == $cmd)
				{
					$this->cmd = $tmp[1];
					if($f_type)
					{
						$this->setReturnType($type);
					}
					if($f_delimiter)
					{
						$this->delimiter = $delimiter;
					}
					return;
				}
			}
		}
		$this->cmd = $alias;
	}
	static public function make($line)
	{
		$line = trim($line);
		$f_delimiter = false;
		$f_set_chk = false;
		$check = false;
		$f_set_type = false;
		$type = self::RETURN_TYPE_RAW;
		if(preg_match('/^>\|([^\s]+)/',$line,$m))
		{
			$line = substr(trim($line), strlen($m[1])+3);
			$check = $m[1];
			$f_set_chk = true;
		}
		$line = trim($line);
		if(preg_match("/^\[(\w*)\] /",$line,$m))
		{
			$line = substr($line, strlen($m[0]));
			if(!!$m[1])
			{
				$delimiter = $m[1];
				$f_delimiter = true;
			}
			$type = self::RETURN_TYPE_ARRAY;
			$f_set_type = true;
		}

		else if(\lib\utility::startsWith($line,"- "))
		{
			$line = substr($line, 2);
			$type = self::RETURN_TYPE_RAW;
			$f_set_type = true;
		}
		$arr = explode(" ", $line, 2);
		if(count($arr) == 2)
		{
			$cmd = new self($arr[0],explode(" ",$arr[1]));
		}
		else
		{
			$cmd = new self($arr[0]);
		}
		if($f_set_chk)
		{
			$cmd->setCheck($check);
		}
		if($f_delimiter)
		{
			$cmd->delimiter = $delimiter;
		}
		if($f_set_type)
		{
			$cmd->setReturnType($type);
		}
		return $cmd;
	}
	public function requireCheck()
	{
		return $this->check !== false;
	}
	public function setCheck($check)
	{
		$this->defChk = false;
		$this->check = $check;
	}
	public function setReturnType($type)
	{
		$this->retType = $type;
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