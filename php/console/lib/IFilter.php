<?php
namespace lib;
interface IFilter
{
	//返回 === true就通过，否则不通过
	function check($str);
}