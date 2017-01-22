<?php
/**
 * @Author: awei.tian
 * @Date: 2017年1月22日
 * @Desc: 
 * 依赖:
 * =============================================
 * c:\xampp\php\php.ini
 * =============================================
[PHP_COM_DOTNET]
extension=php_com_dotnet.dll
com.allow_dcom = true
 */
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

/*
===========================================================================================


$excel = new excel ();
$excel->openxls ( "F:\\newws\\excel\\unitbalance.slk" );

//测试读单元格5，6数据
print $excel->getCell(5, 6) . "\n";//R5C6 == 425

//测试写和保存
$excel->setCell(6, 4, 111);		//test OK
$excel->save();					//TEST OK


$excel->copyrow(1);				//bug已修正，原来使用原来来获取行，每保存一个一个单元格，这个函数的返回值就加1


$excel->save();
$excel->closexls ();

============================================================================================
*/



//=========================================================
//					END
//=========================================================

// $exapp = new COM("Excel.application") or Die ("Did not connect");

// print "Hi";
// #Instantiate the spreadsheet component.
// #    $ex = new COM("Excel.sheet") or Die ("Did not connect");
// $exapp = new COM("Excel.application") or Die ("Did not connect");

// #Get the application name and version
// print "Application name:{$ex->Application->value}<BR>" ;
// print "Loaded version: {$ex->Application->version}<BR>";

// $wkb=$exapp->Workbooks->add();
// #$wkb = $ex->Application->ActiveWorkbook or Die ("Did not open workbook");
// print "we opened workbook<BR>";

// $ex->Application->Visible = 1; #Make Excel visible.
// print "we made excell visible<BR>";

// $sheets = $wkb->Worksheets(1); #Select the sheet
// print "selected a sheet<BR>";
// $sheets->activate; #Activate it
// print "activated sheet<BR>";

// #This is a new sheet
// $sheets2 = $wkb->Worksheets->add(); #Add a sheet
// print "added a new sheet<BR>";
// $sheets2->activate; #Activate it
// print "activated sheet<BR>";

// $sheets2->name="Report Second page";

// $sheets->name="Report First page";
// print "We set a name to the sheet: $sheets->name<BR>";

// # fills a columns
// $maxi=20;
// for ($i=1;$i<$maxi;$i++) {
// 	$cell = $sheets->Cells($i,5) ; #Select the cell (Row Column number)
// 	$cell->activate; #Activate the cell
// 	$cell->value = $i*$i; #Change it to 15000
// }

// $ch = $sheets->chartobjects->add(50, 40, 400, 100); # make a chartobject

// $chartje = $ch->chart; # place a chart in the chart object
// $chartje->activate; #activate chartobject
// $chartje->ChartType=63;
// $selected = $sheets->range("E1:E$maxi"); # set the data the chart uses
// $chartje->setsourcedata($selected); # set the data the chart uses
// print "set the data the chart uses <BR>";

// $file_name="D:/apache/Apache/htdocs/alm/tmp/final14.xls";
// if (file_exists($file_name)) {unlink($file_name);}
// #$ex->Application->ActiveWorkbook->SaveAs($file_name); # saves sheet as final.xls
// $wkb->SaveAs($file_name); # saves sheet as final.xls
// print "saved<BR>";

// #$ex->Application->ActiveWorkbook->Close("False");
// $exapp->Quit();
// unset($exapp);