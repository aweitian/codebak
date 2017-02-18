<?php
abstract class console implements IConsole
{
	protected $handle_read;
	protected $handle_write;
	protected $data_char_set = "UTF-8";
	protected $console_char_set = "GBK";
	protected $fix_charset = false;
	protected $app;
	public function input() 
	{
		$ret = file_get_contents("php://stdin");
		if($this->fix_charset && $this->data_char_set != $this->console_char_set) {
			$ret = iconv($this->console_char_set, $this->data_char_set, $ret);
		}
		return $ret;
	}
	public function output($data) 
	{
		if($this->fix_charset && $this->data_char_set != $this->console_char_set) {
			$data = iconv($this->data_char_set, $this->console_char_set, $data);
		}
		file_put_contents("php://stdout", $data);
		return $this;
	}
	public function show($data)
	{
		$stdout = fopen('php://stderr', 'w');
		if($this->data_char_set != $this->console_char_set)	{
			$data = iconv($this->data_char_set, $this->console_char_set, $data);
		}
		fwrite($stdout,$data);
		fclose($stdout);
		return $this;
	}
	public function setConsoleCharset($charset) {
		$this->console_char_set = $charset;
		return $this;
	}
	public function setDataCharset($charset) {
		$this->data_char_set = $charset;
		return $this;
	}
	public function readLine() 
	{
		$data = trim(fgets(STDIN));
		if($this->data_char_set != $this->console_char_set)	{
			$data = iconv($this->console_char_set, $this->data_char_set, $data);
		}
		return $data;
	}
	public function write($data)
	{
		if($this->data_char_set != $this->console_char_set)	{
			$data = iconv($this->data_char_set, $this->console_char_set, $data);
		}
		fputs(STDOUT,$data);
		return $this;
	}
	public function writeLn($content)
	{
		return $this->write($content."\n");
	}
	public function prompt()
	{
		$this->show('>>> ');
		return $this;
	}
}