<?php
namespace filter;
class intchk implements \lib\IFilter
{
	public function check($str)
	{
		return !!preg_match('/^\d+$/',$str);
	}
}