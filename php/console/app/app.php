<?php
namespace app;

class app
{
	public $root;
	public function __construct($root) 
	{
		$this->root = $root;
		\lib\console::writeStdoutLine("app is starting...");
	}

	public function run($argv) 
	{
		$argc = count($argv);
		if($argc == 1)
		{
			$this->parseChain('default');
		}
		else if($argc == 2)
		{
			$this->parseChain($argv[1]);
		}
		else
		{
			$this->help();
		}
		//\lib\console::writeStdoutLine(count($argv));
	}
	public function help()
	{
		\lib\console::writeStdoutLine('
curl scripts v1.0			
-----------------------------------------------------			
	php artisan
	php artisan job-chain

		');

	}
	private function parseChain($name)
	{
		if(!file_exists($path = $this->root."/job-chain/".$name))
		{
			\lib\console::writeStderrLine("job-chain $name is nonexists");
			exit;
		}
		$phpCmd = [];
		$cmds = file($path);
		foreach ($cmds as $cmd) 
		{
			$cmd = trim($cmd);

			if ($cmd == '' || \lib\utility::startsWith($cmd,'//')) 
			{
				continue;
			}
			if (file_exists($this->root.'/jobs/'.$cmd)) 
			{
				$phpCmd[] = 'php ./jobs/' . $cmd;
			}	
		}
		$cmd = join(' | ',$phpCmd);
		if ($cmd) 
		{
			\lib\system::run($cmd);
			//\lib\console::writeStdout($cmd);
		}
	}
}