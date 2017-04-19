<?php
namespace filter;
class urlchk implements \lib\IFilter
{
	public function check($str)
	{
		return !!preg_match('/^https?:\/\/.+/', $str);
	}

}