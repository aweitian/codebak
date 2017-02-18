<?php

spl_autoload_register(function ($name) 
{
	if($name{0} == "I")
		require __DIR__."/lib/interfaces/".$name.".php";	
	else
		require __DIR__."/lib/class/".$name.".php";	
});

$app = new app(__DIR__);
$app->run($argv);