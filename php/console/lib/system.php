<?php
namespace lib;
class system
{
	static public function run($cmd)
	{
		return system($cmd);
	}
}