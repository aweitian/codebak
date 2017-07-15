<?php
error_reporting(0);
ini_set("display_errors","Off");

// error_reporting(E_ALL);
// ini_set("display_errors","On");

spl_autoload_register(function ($class) {
	$p = '' . str_replace('Tian/','',str_replace('\\','/',$class)) . '.php';
	if(file_exists($p))
    	include $p;
    else if(file_exists($p='lib/'.$p))
    	include $p;
});
$app = new \Tian\App();

$app->route();