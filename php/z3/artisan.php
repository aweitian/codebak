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
		echo 'cache hint...' . "\n";
		return $path;
	}
	exec ( DIR . "/bin/mpq2k.exe e " . file_get_contents ( DIR . "/.map" ) . " Units\HumanUnitFunc.txt cache" );
	exec ( 'cd cache && rename HumanUnitFunc.txt ' . md5 ( 'Units\HumanUnitFunc.txt' ) );
	return $r && extractHumanUnitFuncTxt ( false );
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
	$ret = array ();
	$key = "";
	$handle = @fopen ( $file, "r" );
	if ($handle) {
		while ( ($buffer = fgets ( $handle )) !== false ) {
			$line = trim ( $buffer );
			if (preg_match ( "/^\[(.+)\]$/", $line, $match )) {
				$key = $match [1];
				$ret [$key] = array ();
			} else if (strpos ( $line, "=" ) > 0) {
				$d = explode ( "=", $line, 2 );
				$ret [$key] [$d [0]] = $d [1];
			}
		}
		if (! feof ( $handle )) {
			echo "Error: unexpected fgets() fail\n";
		}
		fclose ( $handle );
	}
	return $ret;
}

// $test = ini ( extractHumanUnitFuncTxt () );
// foreach ( $test as $key => $val ) {
// print $key . " : " . pgbk ( $val ["Name"] ) . "\n";
// }

// $test = new mpq();
// $test->showlist();

// 先从MPQ文件中取出units\balances.slk

$mpq = new mpq ();

// $mpq->showlist();exit;

$mpq->get ( "units\\unitbalance.slk" ); // 测试成功
exit;
$excel = new excel ();
$excel->openxls ( $mpq->getCachePath ( "units\\unitbalance.slk" ) );
print $excel->getCell ( 2, 2 ); // 5 test ok

$excel->setCell ( 2, 2, 50 );
$excel->save ();
$excel->closexls ();

$mpq->replace ( $mpq->getCachePath ( "units\\unitbalance.slk" ), "units\\unitbalance.slk" );//test ok


// =============================================================================
//
// 开始LIB模块
//
// =============================================================================
function msg($str) {
	print pgbk ( $str ) . "\n";
}
function pgbk($str) {
	return iconv ( "UTF-8", "GBK", $str );
}
class excel {
	private $excelApp;
	private $excelWorkBook;
	private $excelSheet;
	private $name = "";
	public function __construct() {
		$this->excelApp = new COM ( "Excel.Application" );
	}
	public function openxls($xls) {
		$this->excelWorkBook = $this->excelApp->Workbooks->open ( $xls );
		$this->excelSheet = $this->excelWorkBook->ActiveSheet;
		$this->name = $xls;
	}
	public function closexls() {
		$this->excelSheet = null;
		$this->excelWorkBook->close ();
		$this->excelApp->Application->Quit ();
		$this->excelApp = null;
	}
	public function setCell($x, $y, $v) {
		$this->excelSheet->Cells ( $x, $y )->value = $v; // cell的值
	}
	public function getCell($x, $y) {
		return $this->excelSheet->Cells ( $x, $y )->value;
	}
	public function save() {
		if (! $this->name) {
			return;
		}
		$this->excelApp->Application->DisplayAlerts = false;
		$this->excelApp->Application->Visible = false;
		$this->excelApp->Application->AlertBeforeOverwriting = false;
		$this->excelWorkBook->SaveAs ( $this->name );
	}
	public function getRows() {
		return $this->excelSheet->usedrange->rows->count;
	}
	public function getColumns() {
		return $this->excelSheet->usedrange->columns->count;
	}
	public function findRows($find, $col = 1) {
		for($i = 1; $i <= $this->getRows (); $i ++) {
			if ($this->getCell ( $i, $col ) == $find) {
				return $i;
			}
		}
		return 0;
	}
	public function copyrow($srcline = -1) {
		if ($srcline < 0) {
			$srcline = $this->getRows ();
		}
		$oldrows = $this->getRows () + 1;
		for($i = 1; $i <= $this->getColumns (); $i ++) {
			$this->setCell ( $oldrows, $i, $this->getCell ( $srcline, $i ) );
		}
		return $this->getRows ();
	}
	public function getRow($line) {
		$ret = array ();
		for($i = 1; $i <= $this->getColumns (); $i ++) {
			$ret [] = $this->getCell ( $line, $i );
		}
		return $ret;
	}
	public function setRow($line, $arr) {
		if (count ( $arr ) != $this->getColumns ()) {
			throw new Exception ( "column does not matched" );
			return;
		}
		for($i = 1; $i <= $this->getColumns (); $i ++) {
			$this->setCell ( $line, $i, $arr [$i - 1] );
		}
	}
}
class mpq {
	private $path;
	public function __construct($path = NULL) {
		if (is_null ( $path )) {
			$path = file_get_contents ( DIR . "/.map" );
		}
		msg ( "Debug: map path is " . $path );
		$this->path = $path;
	}
	public function get($path) {
		try {
			$this->getCachePath ( $path );
			msg ( "Cache: " . $path );
			return;
		} catch ( Exception $e ) {
		}
		system ( DIR . "/bin/mpq2k.exe e " . $this->path . " " . $path . " cache" );
		$this->cache ( $path );
	}
	public function add($src, $dst) {
		system ( DIR . "/bin/mpq2k.exe a " . $this->path . " " . $src . " " . $dst . " /c" );
	}
	public function replace($src, $dst) {
		$this->remove ( $dst );
		$this->add ( $src, $dst );
	}
	public function remove($path) {
		system ( DIR . "/bin/mpq2k.exe d " . $this->path . " " . $path . "" );
	}
	public function showlist() {
		system ( DIR . "/bin/mpq2k.exe l " . $this->path );
	}
	public function getCachePath($path) {
		$path = DIR . "\\cache\\" . md5 ( $path ) . "." . pathinfo ( $path, PATHINFO_EXTENSION );
		if (file_exists ( $path )) {
			return $path;
		}
		throw new Exception ( $path . " NOT FOUND" );
		return false;
	}
	private function cache($path) {
		system ( 'cd cache && rename ' . (end ( explode ( "\\", $path ) )) . ' ' . md5 ( $path ) . "." . pathinfo ( $path, PATHINFO_EXTENSION ) );
	}
}

