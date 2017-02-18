<?php
class app extends console
{
	public $path;
	public function __construct($path) {
		$this->path = $path;
	}
	public function run($argv)
	{
		$this->welcome();
		$this->prompt();
		while(($cmd = $this->readCmd()) !== false)
		{
			$this->dispatch($cmd);
		}		
	}
	private function dispatch($cmd)
	{
		$cmds = explode("|",$cmd);
		array_walk($cmds,function($v,$k){
			$cmdArr = explode(' ',trim($v),2);	
			if(class_exists($cls = trim($cmdArr[0])))
			{
				$rc = new ReflectionClass($cls);
				if ($rc->implementsInterface('IConsole')) 
				{
					$inst = $rc->newInstance($this);
					$method = $rc->getMethod('run');
					if(count($cmdArr) == 2)
						$method->invokeArgs($inst,$cmdArr[1]);
					else
						$method->invokeArgs($inst,['']);
				}
				else
				{
					$this->show('Cmd "' . $cls . '" not found' . "\n");
				}
			}

		});
		$this->prompt();
	}
	public function help()
	{
		$help = file_get_contents($this->path."/.help");
		$this->show($help);
	}
	private function welcome()
	{
		$help = file_get_contents($this->path."/.welcome");
		$this->show($help);
	}
	private function readCmd() 
	{
		$cmd = $this->readLine();
		if('exit' == $cmd )
		{
			$this->writeLn("goodbye");
			return false;	
		}
		return $cmd;
	}	
}
