<?php
/**
 * @Author: awei.tian
 * @Date: 2017年1月23日
 * @Desc: 
 * 依赖:
 */
define ( 'DIR', __DIR__ );
function extractHumanUnitFuncTxt($r = true) {
	if ($path = hitCache ( "Units\HumanUnitFunc.txt" )) {
		echo 'cache hint...'."\n";
		return $path;
	}
	exec ( DIR . "/bin/mpq2k.exe e " . file_get_contents ( DIR . "/.map" ) . " Units\HumanUnitFunc.txt cache" );
	exec ( 'cd cache && rename HumanUnitFunc.txt ' . md5 ( 'Units\HumanUnitFunc.txt' ) );
	return $r && extractHumanUnitFuncTxt(false);
}
function hitCache($str) {
	$c = DIR . "/cache/" . md5 ( $str );
	if (file_exists ( $c ))
		return $c;
	return "";
}
function run() {
}

function ini($file) {
	$ret = array();
	$key = "";
	$handle = @fopen($file, "r");
	if ($handle) {
		while (($buffer = fgets($handle)) !== false) {
			$line = trim($buffer);
			if(preg_match("/^\[(\w{4})\]$/",$line,$match)) {
				$key = $match[1];
				$ret[$key] = array();
			}else if(strpos($line, "=") > 0) {
				$d = explode("=",$line,2);
				$ret[$key][$d[0]] = $d[1];
			}
		}
		if (!feof($handle)) {
			echo "Error: unexpected fgets() fail\n";
		}
		fclose($handle);
	}
	return $ret;
}


$test = ini(extractHumanUnitFuncTxt ()) ;
var_dump($test);