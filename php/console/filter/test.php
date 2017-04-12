<?php
namespace filter;
class test implements \lib\IFilter
{
	public function check($str)
	{
		return trim($str) !== "job2:bbb";
	}
}
