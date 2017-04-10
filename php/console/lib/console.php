<?php
namespace lib;
class console
{
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