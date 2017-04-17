<?php
namespace lib;
class console
{
	//判断当前控制台是不是WINDOWS DOS
	static public function isWinDosConsole()
	{
		// DOS下没有这个变量
		return !isset($_SERVER['_']);
	}
	static public function readStdin()
	{
		return file_get_contents('php://stdin');
	}
	static public function writeStdout($data)
	{
		return file_put_contents('php://stdout', $data);
	}
	static public function writeStdoutLine($data)
	{
		return file_put_contents('php://stdout', $data . "\n");
	}
	static public function writeStderr($data)
	{
		return file_put_contents('php://stderr', $data);
	}
	static public function writeStderrLine($data)
	{
		return file_put_contents('php://stderr', $data . "\n");
	}
}