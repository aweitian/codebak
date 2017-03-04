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
			try {
				$this->dispatch($cmd);
			} catch (Exception $e) {
				$this->show($e->getMessage());
			}
			
		}		
	}
	private function dispatch($cmd)
	{
		$cmds = explode("|",$cmd);
		array_walk($cmds,function($v,$k) use ($pipe){
			$cmdArr = explode(' ',trim($v),2);	
			if(class_exists($cls = trim($cmdArr[0])))
			{
				$rc = new ReflectionClass($cls);
				if ($rc->implementsInterface('IConsole')) 
				{
					$inst = $rc->newInstance($this);
					$pipe->send();
					$method = $rc->getMethod('run');
					if(count($cmdArr) == 2)
						$method->invokeArgs($inst,[array_slice ( $cmdArr, 1 )] );
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
		foreach (glob($this->path."/lib/modules/*.php") as $filename) {
			$this->show( 'Type "help ' . pathinfo($filename,PATHINFO_FILENAME) .'" for more infomation.' . "\n");
		}

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
