<?php
spl_autoload_register(function ($name) {
	if($name{0} == "I")
	{
		require __DIR__."/lib/interfaces/".$name.".php";	
	}
	else
	{
		require __DIR__."/lib/class/".$name.".php";	
	}
});
$help = '
artisan 数据采集第三版
----------------------------------------
feed data.txt
fetch http://www.example.com
filter rule
';
$app = new console();
$app->run($argv);