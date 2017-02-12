<?php
/**
顺序		格式		含义
第一行		ID;…		文件头，包含文件的创建者（使用啥软件创建的），Excel = PWXL 文件是否是被保护的
紧随第二行	P;P…		单元格格式-数字、文本
紧随“P;P…”	P;F…P;E…	字体样式（字体、颜色、字号）
自定义		B;…		告知电子表格边界
自定义		F;…		设置格式
自定义		C;…		设置内容
自定义		O;…		选项
最后一行	E		结束符号

自己测试观察
=========================================================
字符串类型表双引号包括.
;号为转义字符

*/
//http://wenku.baidu.com/linkurl=NF8ddS5SrfY2ko2tgSvJ65kBirhVTQTe9ZHXzLRgYv0TG66xX_KVIkI1HAmMzh8dLJ3YDfTa7-bN0GczZzvzoDTl-Kp8oNFuOz-7z_oZNeN3akiX6319OLUMkNu-rWEE
class slk 
{
	//如果没有数据就没有BOUND
	protected $file = NULL;
	private $x = 0; //坐标X,第几列(内部游标)
	private $y = 0; //坐标Y,第几行(内部游标)
	private $header = "ID;PWXL;N;E";
	private $options = [];
	private $rows = 0; //整个表格的行数,0表示没有数据
	private $cols = 0; //整个表格的列数
	private $bound = "";
	private $line = "\r\n";
	private $data = [];
	
	public function __construct($file_name) 
	{
		$this->file = $file_name;
	}
	private function stringFromColumnIndex($pColumnIndex = 0)
	{
		//	Using a lookup cache adds a slight memory overhead, but boosts speed
		//	caching using a static within the method is faster than a class static,
		//		though it's additional memory overhead
		static $_indexCache = array();

		if (!isset($_indexCache[$pColumnIndex])) 
		{
			// Determine column string
			if ($pColumnIndex < 26) 
			{
				$_indexCache[$pColumnIndex] = chr(65 + $pColumnIndex);
			} 
			elseif ($pColumnIndex < 702) 
			{
				$_indexCache[$pColumnIndex] = chr(64 + ($pColumnIndex / 26)) .
											  chr(65 + $pColumnIndex % 26);
			} 
			else 
			{
				$_indexCache[$pColumnIndex] = chr(64 + (($pColumnIndex - 26) / 676)) .
											  chr(65 + ((($pColumnIndex - 26) % 676) / 26)) .
											  chr(65 + $pColumnIndex % 26);
			}
		}
		return $_indexCache[$pColumnIndex];
	}
	private function _unwrapResult($value) 
	{
		if (is_string($value)) 
		{
			if ((isset($value{0})) && ($value{0} == '"') && (substr($value,-1) == '"')) 
			{
				return substr($value,1,-1);
			}
		} 
		else if((is_float($value)) && ((is_nan($value)) || (is_infinite($value)))) 
		{
			return '#NUM!';
		}
		return $value;
	}
	private function _wrapResult($value) 
	{
		if (preg_match("/^\d+(\.?\d*)?$/",$value)) 
		{
			return $value;
			
		} 
		return '"' . str_replace(';',';;',$value) . '"';
	}
	public function open() 
	{
		if(!file_exists($this->file))return;
		$handle = fopen($this->file,'r');
		while (($rowData = fgets($handle)) !== FALSE) 
		{
			$line = rtrim($rowData);
			//第一个字符为 " 则最后一个也必须为 "否则出错,去除前后",把中间的两个;替换成一个; 因为 ;为转义字符
			$rowData = explode("\t",str_replace('¤',';',str_replace(';',"\t",str_replace(';;','¤',$line))));
			$dataType = array_shift($rowData);
			if ($dataType == 'C') 
			{
				foreach($rowData as $rowDatum) 
				{
					switch($rowDatum{0}) 
					{
						case 'C' :
						case 'X' :
							$this->x = substr($rowDatum,1);
							break;
						case 'R' :
						case 'Y' :
							$this->y = substr($rowDatum,1);
							break;
						case 'K':
							if(!isset($this->data[$this->y]))
							{
								$this->data[$this->y] = [];
							}
							$this->data[$this->y][$this->x] = $this->_unwrapResult(substr($rowDatum,1));
							break;
						case 'E' :
							//暂时不处理这类型
							break;
							$cellDataFormula = '='.substr($rowDatum,1);
							//	Convert R1C1 style references to A1 style references (but only when not quoted)
							$temp = explode('"',$cellDataFormula);
							$key = false;
							foreach($temp as &$value) {
								//	Only count/replace in alternate array entries
								if ($key = !$key) {
									preg_match_all('/(R(\[?-?\d*\]?))(C(\[?-?\d*\]?))/',$value, $cellReferences,PREG_SET_ORDER+PREG_OFFSET_CAPTURE);
									//	Reverse the matches array, otherwise all our offsets will become incorrect if we modify our way
									//		through the formula from left to right. Reversing means that we work right to left.through
									//		the formula
									$cellReferences = array_reverse($cellReferences);
									//	Loop through each R1C1 style reference in turn, converting it to its A1 style equivalent,
									//		then modify the formula to use that new reference
									foreach($cellReferences as $cellReference) {
										$rowReference = $cellReference[2][0];
										//	Empty R reference is the current row
										if ($rowReference == '') $rowReference = $row;
										//	Bracketed R references are relative to the current row
										if ($rowReference{0} == '[') $rowReference = $row + trim($rowReference,'[]');
										$columnReference = $cellReference[4][0];
										//	Empty C reference is the current column
										if ($columnReference == '') $columnReference = $column;
										//	Bracketed C references are relative to the current column
										if ($columnReference{0} == '[') $columnReference = $column + trim($columnReference,'[]');
										$A1CellReference = $this->stringFromColumnIndex($columnReference-1).$rowReference;

										$value = substr_replace($value,$A1CellReference,$cellReference[0][1],strlen($cellReference[0][0]));
									}
								}
							}
							unset($value);
							//	Then rebuild the formula string
							$cellDataFormula = implode('"',$temp);
							$hasCalculatedValue = true;
							break;
						default:
							break;
					}
				}
			}
			else if($dataType == 'P')
			{
				$this->options[] = $line;
			}
			else if($dataType == 'F')
			{
				$this->options[] = $line;
			}
			else if($dataType == 'O')
			{
				$this->options[] = $line;
			}
			else if($dataType == 'B')
			{
				foreach($rowData as $rowDatum) 
				{
					switch($rowDatum{0}) 
					{
						case 'C' :
						case 'X' :
							$this->cols = substr($rowDatum,1);
							break;
						case 'R' :
						case 'Y' :
							$this->rows = substr($rowDatum,1);
							break;
						default:
							break;
					}
				}
			}
			else if($dataType == 'E')
			{
				
			}
		}
		fclose($handle);
		
	}
	public function close()
	{

	}
	
	
	protected function setHeader($pwxl="PWXL") 
	{
		$this->header = strtr($this->header,array("PWXL" => $pwxl));
	}
	
	/***
	 * Y就是几行，X就是几列
	 */
	protected function setBound($rows,$cols) 
	{
		if ($rows > 0 && $cols > 0) 
		{
			$this->rows = $rows;
			$this->cols = $cols;
			$this->bound = "B;Y{$rows};X{$cols};D0 0 ".($rows-1)." ".($cols-1);			
		}
	}
	protected function syncBound() 
	{
		$this->bound = "B;Y".$this->rows.";X".$this->cols.";D0 0 ".($this->rows-1)." ".($this->cols-1);			
	}
	public function setCell($row, $col, $v) 
	{
		if($col > $this->cols)
		{
			$this->cols = $col;	
		}
		if($row > $this->rows)
		{
			$this->rows = $row;	
		}
		if(!isset($this->data[$row]))
		{
			$this->data[$row] = [];
		}
		$this->data[$row][$col] = $v;
	}
	public function getRows() 
	{
		return $this->rows;
	}
	public function getColumns() 
	{
		return $this->cols;
	}
	public function findRows($find, $col = 1) 
	{
		for($i = 1; $i <= $this->getRows (); $i ++) 
		{
			if ($this->getCell ( $i, $col ) == $find) 
			{
				return $i;
			}
		}
		return 0;
	}
	public function copyrow($srcline = -1) 
	{
		if ($srcline < 0) 
		{
			$srcline = $this->getRows ();
		}
		$oldrows = $this->getRows () + 1;
		for($i = 1; $i <= $this->getColumns (); $i ++) 
		{
			$this->setCell ( $oldrows, $i, $this->getCell ( $srcline, $i ) );
		}
		return $this->getRows ();
	}
	public function getCell($row,$col) 
	{
		return $this->getRaw ( $row,$col );
	}
	
	/**
	 * 下标从1开始,
	 */
	protected function getRaw($row,$col) 
	{
		if($row > $this->rows || $col > $this->cols) 
		{
			throw new Exception("invalid row or col");
		}
		if(isset($this->data[$row][$col])) 
		{
			return $this->data[$row][$col];
		}
		else
		{
			return null;
		}
	}
	public function getRow($line) 
	{
		$ret = array ();
		for($i = 1; $i <= $this->getColumns (); $i ++) 
		{
			$ret [] = $this->getCell ( $line, $i );
		}
		return $ret;
	}
	public function setRow($line, $arr) 
	{
		if (count ( $arr ) != $this->getColumns ()) 
		{
			throw new Exception ( "column does not matched" );
			return;
		}
		for($i = 1; $i <= $this->getColumns (); $i ++) 
		{
			$this->setCell ( $line, $i, $arr [$i - 1] );
		}
	}
	public function debug() 
	{
		print 'rows' .  $this->rows . "\n";
		print 'cols' .  $this->cols . "\n";
		var_dump($this->data);
	}
	public function save() 
	{
		$handle = fopen($this->file,'w');
		if(!$handle)
		{
			throw new Exception("open file for write failed.");
				
		}
		$write = function($line) use ($handle) {
			fwrite($handle,$line.$this->line);	
		};
		$write($this->header);
		foreach($this->options as $op)
		{
			$write($op);	
		}
		if(empty($this->data))
		{
			$write("E");	
			return;
		}
		$this->syncBound();
		$write($this->bound);
		for($i=1;$i<=$this->rows;$i++)
		{
			for($j=1;$j<=$this->cols;$j++)
			{
				if(isset($this->data[$i][$j]))
				{
					$write("C;Y".($i).";X".($j).";K".$this->_wrapResult($this->data[$i][$j]));	
				}
			}	
		}
		$write("E");	
		fclose($handle);
		return;
	}
	
	public function __destruct()
	{
		
	}
}

$test = new slk("www.slk");
$test->open();
$test->debug();
$test->setCell(5,4,221);
$test->setCell(1,2,1212);
$test->setCell(6,5,";;tian");

$test->save();
//$test->debug();
//var_dump( $test->getCell(5,4) );
$test->close();