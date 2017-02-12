<?php
/**
 * @Author: awei.tian
 * @Date: 2017年1月23日
 * @Desc: 
 * 依赖:
 */
define ( 'DIR', __DIR__ );
define ( 'VER', 1.1 );
if (PHP_SAPI != "cli") {
	exit ( "RUN CLI MODE." );
}
$alias = array (
	"mb" => "modifyCommonHeroesBalance" 
);

run ( $argv );
function help() {
	print (pgbk ( '
artisan (ver:' . VER . ')
=====================================		
	uid [-a] [uid|zg]
	mb  all|wg|sg|h004,h008 +5|-5 OR 1,1,1	#modifyCommonHeroesBalance
		
		' ))

	 ;
}
function extractHumanUnitFuncTxt($r = true) {
	if ($path = hitCache ( "Units\HumanUnitFunc.txt" )) {
		// echo 'cache hint...' . "\n";
		return $path;
	}
	exec ( "\"" . DIR . "\\bin\\mpq2k.exe\" e " . file_get_contents ( DIR . "/.map" ) . " Units\HumanUnitFunc.txt cache" );
	exec ( 'cd cache && rename HumanUnitFunc.txt ' . md5 ( 'Units\HumanUnitFunc.txt' ) );
	return $r && extractHumanUnitFuncTxt ( false );
}
function hitCache($str) {
	$c = DIR . "/cache/" . md5 ( $str );
	if (file_exists ( $c ))
		return $c;
	return "";
}
function uid($a) {
	$l = count ( $a );
	if ($l == 0) {
		foreach ( getWeigouHeroesId () as $hero ) {
			msg ( $hero . " " . getHeroName ( $hero ) );
		}
		msg ( '----------' );
		foreach ( getShugouHeroesId () as $hero ) {
			msg ( $hero . " " . getHeroName ( $hero ) );
		}
	}
}
function modifyCommonHeroesBalance($a) {
	$mpq = new mpq ();
	$mpq->get ( "units\\unitbalance.slk" );
	$excel = new slk ($mpq->getCachePath ( "units\\unitbalance.slk" ));
	$excel->open ( );
	$write = false;
	$l = count ( $a );
	$r = function ($data) use ($excel, $mpq) {
		foreach ( $data as $hero ) {
			$index = $excel->findRows ( $hero );
			msg ( "--" . getHeroName ( $hero ) . " str plus:" . $excel->getCell ( $index, 38 ) . ",agi plus:" . $excel->getCell ( $index, 40 ) . ",int plus:" . $excel->getCell ( $index, 39 ) );
		}
	};
	$w = function ($data, $closure) use ($excel, $mpq) {
		foreach ( $data as $hero ) {
			$index = $excel->findRows ( $hero );
			$str = $closure ( 0, $excel->getCell ( $index, 38 ) );
			$int = $closure ( 1, $excel->getCell ( $index, 39 ) );
			$agi = $closure ( 2, $excel->getCell ( $index, 40 ) );
			$excel->setCell ( $index, 38, $str );
			$excel->setCell ( $index, 39, $int );
			$excel->setCell ( $index, 40, $agi );
			msg ( "正在设置:" . getHeroName ( $hero ) . "str:" . $str . ",int:" . $int . ",agi:" . $agi );
		}
	};
	if ($l == 0) {
		$m ( array_merge ( getWeigouHeroesId (), getShugouHeroesId () ) );
	} else if ($l == 1) {
		switch ($a [0]) {
			case "all" :
				$r ( array_merge ( getWeigouHeroesId (), getShugouHeroesId () ) );
				break;
			case "wg" :
				$r ( getWeigouHeroesId () );
				break;
			case "sg" :
				$r ( getShugouHeroesId () );
				break;
			default :
				$r ( explode ( ",", $a [0] ) );
				break;
		}
	} else if ($l == 2) {
		if (preg_match ( "/^\+(\d+)$/", $a [1], $m )) {
			$plus = $m [1];
			// $a = 0 str,1 int 2 agi
			$closure = function ($a, $b) use ($plus) {
				return $b + $plus;
			};
		} else if (preg_match ( "/^\-(\d+)$/", $a [1], $m )) {
			$plus = intval ( $m [1] );
			// $a = 0 str,1 int 2 agi
			$closure = function ($a, $b) use ($plus) {
				return $b - $plus;
			};
		} else if (preg_match ( "/^(\d+),(\d+),(\d+)$/", $a [1], $m )) {
			$str = $m [1];
			$int = $m [2];
			$agi = $m [3];
			$closure = function ($a, $b) use ($str, $int, $agi) {
				switch ($a) {
					case 0 :
						return $str;
					case 1 :
						return $int;
					default :
						return $agi;
				}
			};
		} else {
			help ();
			return;
		}
		
		switch ($a [0]) {
			case "all" :
				$w ( array_merge ( getWeigouHeroesId (), getShugouHeroesId () ), $closure );
				$excel->save ();
				$write = true;
				break;
			case "wg" :
				$w ( getWeigouHeroesId (), $closure );
				$excel->save ();
				$write = true;
				break;
			case "sg" :
				$w ( getShugouHeroesId (), $closure );
				$excel->save ();
				$write = true;
				break;
			default :
				$w ( explode ( ",", $a [0] ), $closure );
				$excel->save ();
				$write = true;
				break;
		}
	} else {
		help ();
	}
	if ($write) {
		$mpq->replace ( $mpq->getCachePath ( "units\\unitbalance.slk" ), "units\\unitbalance.slk" );
	}
}
function getHeroName($id) {
	$data = ini ( extractHumanUnitFuncTxt () );
	if (array_key_exists ( $id, $data )) {
		return $data [$id] ["Name"];
	}
	return "";
}
function getWeigouHeroesId() {
	return array (
			'U00A',
			'U00I',
			'U000',
			'H006',
			'U007' 
	);
}
function getShugouHeroesId() {
	return array (
			'H008',
			'O003',
			'O002',
			'H004',
			'E000' 
	);
}
function run($a) {
	global $alias;
	$l = count ( $a );
	if ($l < 2) {
		help ();
	} else {
		if (function_exists ( $a [1] )) {
			call_user_func ( $a [1], array_slice ( $a, 2 ) );
		} else if (isset ( $alias [$a [1]] ) && function_exists ( $alias [$a [1]] )) {
			call_user_func ( $alias [$a [1]], array_slice ( $a, 2 ) );
		} else {
			help ();
		}
	}
}

// $test = ini ( extractHumanUnitFuncTxt () );
// foreach ( $test as $key => $val ) {
// print $key . " : " . pgbk ( $val ["Name"] ) . "\n";
// }

// $test = new mpq();
// $test->showlist();

// 先从MPQ文件中取出units\balances.slk

// $mpq = new mpq ();

// $mpq->showlist();exit;

// $mpq->get ( "units\\unitbalance.slk" ); // 测试成功
// exit;
// $excel = new excel ();
// $excel->openxls ( $mpq->getCachePath ( "units\\unitbalance.slk" ) );
// print $excel->getCell ( 2, 2 ); // 5 test ok

// $excel->setCell ( 2, 2, 50 );
// $excel->save ();
// $excel->closexls ();

// $mpq->replace ( $mpq->getCachePath ( "units\\unitbalance.slk" ), "units\\unitbalance.slk" );//test ok

// =============================================================================
//
// 开始LIB模块
//
// =============================================================================
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
function msg($str) {
	print pgbk ( $str ) . "\n";
}
function pgbk($str) {
	return iconv ( "UTF-8", "GBK", $str );
}
class mpq {
	private $path;
	public function __construct($path = NULL) {
		if (is_null ( $path )) {
			$path = file_get_contents ( DIR . "/.map" );
		}
		// msg ( "Debug: map path is " . $path );
		$this->path = $path;
	}
	public function get($path) {
		try {
			$this->getCachePath ( $path );
			// msg ( "Cache: " . $path );
			return;
		} catch ( Exception $e ) {
		}
		system ( "\"" . DIR . "\\bin\\mpq2k.exe\" e " . $this->path . " " . $path . " cache" );
		$this->cache ( $path );
	}
	public function add($src, $dst) {
		system ( "\"" . DIR . "\\bin\\mpq2k.exe\" a " . $this->path . " \"" . $src . "\" " . $dst . " /c" );
	}
	public function replace($src, $dst) {
		$this->remove ( $dst );
		$this->add ( $src, $dst );
	}
	public function remove($path) {
		system ( "\"" . DIR . "\\bin\\mpq2k.exe\" d " . $this->path . " " . $path . "" );
	}
	public function showlist() {
		system ( "\"" . DIR . "\\bin\\mpq2k.exe\" l " . $this->path );
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
class slk {
	// 如果没有数据就没有BOUND
	protected $file = NULL;
	private $x = 0; // 坐标X,第几列(内部游标)
	private $y = 0; // 坐标Y,第几行(内部游标)
	private $header = "ID;PWXL;N;E";
	private $options = [ ];
	private $rows = 0; // 整个表格的行数,0表示没有数据
	private $cols = 0; // 整个表格的列数
	private $bound = "";
	private $line = "\r\n";
	private $data = [ ];
	public function __construct($file_name) {
		$this->file = $file_name;
	}
	private function _unwrapResult($value) {
		if (is_string ( $value )) {
			if ((isset ( $value {0} )) && ($value {0} == '"') && (substr ( $value, - 1 ) == '"')) {
				return substr ( $value, 1, - 1 );
			}
		} else if ((is_float ( $value )) && ((is_nan ( $value )) || (is_infinite ( $value )))) {
			return '#NUM!';
		}
		return $value;
	}
	private function _wrapResult($value) {
		if (preg_match ( "/^\d+(\.?\d*)?$/", $value )) {
			return $value;
		}
		return '"' . str_replace ( ';', ';;', $value ) . '"';
	}
	public function open() {
		if (! file_exists ( $this->file ))
			return;
		$handle = fopen ( $this->file, 'r' );
		while ( ($rowData = fgets ( $handle )) !== FALSE ) {
			$line = rtrim ( $rowData );
			// 第一个字符为 " 则最后一个也必须为 "否则出错,去除前后",把中间的两个;替换成一个; 因为 ;为转义字符
			$rowData = explode ( "\t", str_replace ( '¤', ';', str_replace ( ';', "\t", str_replace ( ';;', '¤', $line ) ) ) );
			$dataType = array_shift ( $rowData );
			if ($dataType == 'C') {
				foreach ( $rowData as $rowDatum ) {
					switch ($rowDatum {0}) {
						case 'C' :
						case 'X' :
							$this->x = substr ( $rowDatum, 1 );
							break;
						case 'R' :
						case 'Y' :
							$this->y = substr ( $rowDatum, 1 );
							break;
						case 'K' :
							if (! isset ( $this->data [$this->y] )) {
								$this->data [$this->y] = [ ];
							}
							$this->data [$this->y] [$this->x] = $this->_unwrapResult ( substr ( $rowDatum, 1 ) );
							break;
						default :
							break;
					}
				}
			} else if ($dataType == 'P') {
				$this->options [] = $line;
			} else if ($dataType == 'F') {
				$this->options [] = $line;
			} else if ($dataType == 'O') {
				$this->options [] = $line;
			} else if ($dataType == 'B') {
				foreach ( $rowData as $rowDatum ) {
					switch ($rowDatum {0}) {
						case 'C' :
						case 'X' :
							$this->cols = substr ( $rowDatum, 1 );
							break;
						case 'R' :
						case 'Y' :
							$this->rows = substr ( $rowDatum, 1 );
							break;
						default :
							break;
					}
				}
			} else if ($dataType == 'E') {
			}
		}
		fclose ( $handle );
	}
	public function close() {
	}
	protected function setHeader($pwxl = "PWXL") {
		$this->header = strtr ( $this->header, array (
				"PWXL" => $pwxl 
		) );
	}
	protected function syncBound() {
		$this->bound = "B;Y" . $this->rows . ";X" . $this->cols . ";D0 0 " . ($this->rows - 1) . " " . ($this->cols - 1);
	}
	public function setCell($row, $col, $v) {
		if ($col > $this->cols) {
			$this->cols = $col;
		}
		if ($row > $this->rows) {
			$this->rows = $row;
		}
		if (! isset ( $this->data [$row] )) {
			$this->data [$row] = [ ];
		}
		$this->data [$row] [$col] = $v;
	}
	public function getRows() {
		return $this->rows;
	}
	public function getColumns() {
		return $this->cols;
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
	public function getCell($row, $col) {
		return $this->getRaw ( $row, $col );
	}
	
	/**
	 * 下标从1开始,
	 */
	protected function getRaw($row, $col) {
		if ($row > $this->rows || $col > $this->cols) {
			throw new Exception ( "invalid row or col" );
		}
		if (isset ( $this->data [$row] [$col] )) {
			return $this->data [$row] [$col];
		} else {
			return null;
		}
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
	public function debug() {
		print 'rows' . $this->rows . "\n";
		print 'cols' . $this->cols . "\n";
		var_dump ( $this->data );
	}
	public function save() {
		$handle = fopen ( $this->file, 'w' );
		if (! $handle) {
			throw new Exception ( "open file for write failed." );
		}
		$write = function ($line) use ($handle) {
			fwrite ( $handle, $line . $this->line );
		};
		$write ( $this->header );
		foreach ( $this->options as $op ) {
			$write ( $op );
		}
		if (empty ( $this->data )) {
			$write ( "E" );
			return;
		}
		$this->syncBound ();
		$write ( $this->bound );
		for($i = 1; $i <= $this->rows; $i ++) {
			for($j = 1; $j <= $this->cols; $j ++) {
				if (isset ( $this->data [$i] [$j] )) {
					$write ( "C;Y" . ($i) . ";X" . ($j) . ";K" . $this->_wrapResult ( $this->data [$i] [$j] ) );
				}
			}
		}
		$write ( "E" );
		fclose ( $handle );
		return;
	}
}