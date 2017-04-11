<?php
namespace app;
class cmd
{
	const RETURN_TYPE_ARRAY = '[]';
	const RETURN_TYPE_RAW = 'V';
	public $cmd;
	public $retType;
	public $argv;

	public function __construct($cmd,$ret = self::RETURN_TYPE_RAW,$argv = [])
	{
		$this->cmd = $cmd;
		$this->retType = $ret;
		$this->argv = $argv;
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