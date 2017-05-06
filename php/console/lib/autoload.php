<?php
spl_autoload_register(function ($name) 
{
	$name = str_replace("\\","/",$name);
	if (file_exists($path = dirname(__DIR__)."/".$name.".php")) 
		require $path;
});