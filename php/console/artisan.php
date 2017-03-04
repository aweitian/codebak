<?php

spl_autoload_register(function ($name) 
{
	if($name{0} == "I")
		require __DIR__."/lib/interfaces/".$name.".php";	
	else if (file_exists($path = __DIR__."/lib/class/".$name.".php")) 
		require $path;
	else if (file_exists($path = __DIR__."/lib/modules/".$name.".php")) 
		require $path;	
});

$app = new app(__DIR__);
$app->run($argv);