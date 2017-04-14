<?php
namespace app;
// 一行命令格式 第一个为返回值 有效值为V,[]
//				第二个为命令或者别名
//				后面全部为参数
class app
{
	public $root;
	private $cmdList = [];
	private $debug = false;
	private $options = [];
	public function __construct($root) 
	{
		$this->root = $root;
		\lib\console::writeStdoutLine("app is starting...");
		$this->init();
	}
	private function init() 
	{
		if(!file_exists($this->root."/runtime"))
		{
			\lib\system::run("mkdir runtime");
		}
		$this->options['silient'] = true;
		//\lib\system::run("cd runtime && echo > .stdin && echo > .stdout && echo > .stderr");
	}
	public function setDebugFlag()
	{
		$this->debug = true;
	}
	public function run($argv) 
	{
		$argc = count($argv);
		if($argc > 1)
		{
			foreach ($argv as $arg) 
			{
				switch ($arg) 
				{
					case '--debug':
					case '-d':
						$this->setDebugFlag();
						break;
					case '--lineoutput':
					case '-l':
						$this->options['lineoutput'] = true;
						break;
					case '--silient':
					case '-s':
						$this->options['silient'] = false;
						break;
					case '--help':
					case '-h':						
						$this->help();
					default:
						
						break;
				}
			}
			if(!\lib\utility::startsWith($argv[1],'-'))
			{
				$this->parseChain($argv[1]);
			}
		}
		//\lib\console::writeStdoutLine(count($argv));
	}
	public function help()
	{
		\lib\console::writeStdoutLine('
curl scripts v1.0			
=======================================================			
  php artisan
  php artisan job-chain

  options:
  -----------------------------------------------------
    -d/--debug set debug flag
    -h/--help show those message
    -l/--lineoutput show stdoutput with line seperator
    -s/--silient show silient message
		');

	}
	private function parseChain($name)
	{
		if(!file_exists($path = $this->root."/job-chain/".$name))
		{
			\lib\console::writeStderrLine("job-chain $name is nonexists");
			exit;
		}
		$this->cmdList = [];
		$cmds = file($path);
		foreach ($cmds as $cmd) 
		{
			$cmd = trim($cmd);
			if ($cmd == '' || \lib\utility::startsWith($cmd,'//')) 
			{
				continue;
			}
			$this->cmdList[] = $cmd;
		}
		$this->execCmdList("",0);
	}

	private function execCmdList($stdin,$cur=0)
	{
		//var_dump($this->cmdList);
		if($cur >= count($this->cmdList))
		{
			if(array_key_exists('lineoutput', $this->options) && $this->options['lineoutput'] == true)
			{
				\lib\console::writeStdoutLine($stdin);
			}
			else
			{
				\lib\console::writeStdout($stdin);
			}
			return;
		}
		$cmd = $this->cmdList[$cur];
		$cmd = $this->makeCmd($cmd);
		//默认第一条命令不检查STDIN
		if ($cur == 0 && $cmd->defChk )
		{
			$cmd->setCheck(false);
		}
		if($cmd->requireCheck())
		{
			if(!is_string($cmd->check))
			{
				\lib\console::writeStderrLine("invalid cmd:" . $this->cmdList[$cur]);
				return;
			}
			if(!\lib\utility::startsWith($cmd->check,"\\"))
			{
				$cls = "\\filter\\" . $cmd->check;
			}
			else
			{
				$cls = $cmd->check;
			}
			try
			{
				$rc = new \ReflectionClass($cls);
			}
			catch(\ReflectionException $e)
			{
				\lib\console::writeStderrLine("filter class: ($cls) not found.");
				return;
			}
			
			if (!$rc->implementsInterface("\\lib\\IFilter"))
			{
				\lib\console::writeStderrLine("invalid cmd:" . $this->cmdList[$cur]);
				return;
			}
			$controller = $rc->newInstance();
			$method = $rc->getMethod("check");
			$ret = $method->invokeArgs($controller, array($stdin));
			if($ret !== true)
			{
				if($this->options['silient'] === false)
				{
					\lib\console::writeStderrLine("block stdin:$stdin,cmd:".$cmd->getCmd());
				}
				return;
			}
		}
		$output = $this->execCmd($cmd,$stdin);
		if($cmd->isArrayReturn())
		{
			$cmd = explode("\n",$output);
			foreach($cmd as $c)
			{
				$this->execCmdList($c,$cur+1);
			}			
		}
		else
		{
			$this->execCmdList($output,$cur+1);
		}
	}

	private function parseLine($cmd)
	{
		$cmd = trim($cmd);
		if ($cmd == '' || \lib\utility::startsWith($cmd,'//')) 
		{
			continue;
		}
		$this->makdCmd($cmd);
	}
	private function makeCmd($line)
	{
		return \app\cmd::make($line);
	}
	/**
	 * @return stdout | null
	 */
	public function execCmd(\app\cmd $cmd,$stdin)
	{
		$argv = join(" ",$cmd->argv);
		$cmd = $cmd->cmd . ($argv ? (" " . $argv) : "");
		return $this->exec($cmd,$stdin);
	}

	/**
	 * @return stdout | null
	 */
	public function exec($cmd,$stdin)
	{
		$descriptorspec = array(
		   0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
		   1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
		   STDERR
		   //2 => array("file", "./error-output.txt", "a") // stderr is a file to write to
		);
		

		//\lib\system::run($cmd->cmd . ($argv ? $argv : "")));


		//debug
		if($this->debug)
	    {
	    	\lib\console::writeStderrLine("<<< ".$stdin);
	    	\lib\console::writeStderrLine("--> ".$cmd);
	    }
		//\lib\console::writeStderrLine("--> ".$cmd);


		$process = proc_open($cmd, $descriptorspec, $pipes);
		if (is_resource($process)) 
		{
		    // $pipes now looks like this:
		    // 0 => writeable handle connected to child stdin
		    // 1 => readable handle connected to child stdout
		    // Any error output will be appended to /tmp/error-output.txt

		    fwrite($pipes[0], $stdin);
		    fclose($pipes[0]);

		    $ret = stream_get_contents($pipes[1]);
		    fclose($pipes[1]);

		    // It is important that you close any pipes before calling
		    // proc_close in order to avoid a deadlock
		    $return_value = proc_close($process);

		    //echo "command returned $return_value\n";



		    //debug
		    if($this->debug)
		    {
		    	\lib\console::writeStderrLine(">>> ".$ret);
		    	\lib\console::writeStderrLine("---------------------");
		    }
			//
		    return $ret;
		}
		else
		{
			\lib\console::writeStderrLine("invalid cmd:" . $cmd);
			return null;
		}
		
	}

	// private function setStdin($data)
	// {
	// 	return file_put_contents($this->root."/runtime/.stdin",$data);
	// }

	// private function setStdout($data)
	// {
	// 	return file_put_contents($this->root."/runtime/.stdout",$data);
	// }

	// private function setStderr($data)
	// {
	// 	return file_put_contents($this->root."/runtime/.stderr",$data);
	// }

	// public function getStdin()
	// {
	// 	return file_get_contents($this->root."/runtime/.stdin");
	// }

	// public function getStdout()
	// {
	// 	return file_get_contents($this->root."/runtime/.stdout");
	// }

	// public function getStderr()
	// {
	// 	return file_get_contents($this->root."/runtime/.stderr");
	// }
}