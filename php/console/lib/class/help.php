<?PHP
class help implements IConsole
{
	public function run($argv)
	{
		$this->welcome();
		$this->prompt();
		$stdin = fopen('php://stdin', 'r');
		while(($cmd = $this->readCmd()) !== false)
		{
			$this->dispatch($cmd);
			
			
			
		}		
	}
	private function prompt()
	{
		$this->show('>>>');
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
					$inst = $rc->newInstance();
					$method = $rc->getMethod('run');
					if(count($cmdArr) == 2)
						$method->invokeArgs();
					else
						$method->invokeArgs($cmdArr[1]);
				}
			}
		});
		$this->prompt();
	}
	private function write($content,$conv = true)
	{
		$stdout = fopen('php://stdout', 'w');
		if($conv)
		{
			$content = iconv("UTF-8","GBK",$content);
		}
		fwrite($stdout,$content);
		fclose($stdout);
	}
	private function show($content,$conv = true)
	{
		$stdout = fopen('php://stderr', 'w');
		if($conv)
		{
			$content = iconv("UTF-8","GBK",$content);
		}
		fwrite($stdout,$content);
		fclose($stdout);
	}
	private function writeLn($content)
	{
		return $this->write($content."\n");
	}
	public function help()
	{
		global $help;
		$this->show($help);
	}
	private function welcome()
	{
		$this->show('		
artisan data fetch ver 3.0 (awei.tian @ 2017-2-17)
---------------------------------------------------
	type help for more infoes
');
	}
	private function readCmd() 
	{
		$cmd = trim(fgets(STDIN));
		if('exit' == $cmd )
		{
			$this->writeLn("goodbye");
			return false;	
		}
		return $cmd;
	}	
}