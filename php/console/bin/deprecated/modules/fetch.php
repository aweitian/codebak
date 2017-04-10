<?php
class fetch extends console 
{



	public function run($argv)
	{
		if(!isset($argv[0]))
		{
			$this->help();
		}
		else
		{
			system("curl --silent ".$argv[0]);
		}
	}
}


