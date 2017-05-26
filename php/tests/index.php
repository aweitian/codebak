<?php
spl_autoload_register(function ($class) {
	$p = '' . str_replace('Tian/','',str_replace('\\','/',$class)) . '.php';
	if(file_exists($p))
    	include $p;
    else if(file_exists($p='lib/'.$p))
    	include $p;
});
$app = new \Tian\App();

$app->route();