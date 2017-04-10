<?php
spl_autoload_register(function ($name) 
{
	if (file_exists($path = dirname(__DIR__)."/".$name.".php")) 
		require $path;
});