<?php
class u2g extends console
{
	public function run($argv)
	{
		return iconv('UTF-8','GBK',$argv[0]);
	}
}