<?php
namespace filter;
class emptychk implements \lib\IFilter
{
	public function check($str)
	{
		return !!$str;
	}
}
