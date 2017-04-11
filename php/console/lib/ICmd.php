<?php
interface ICmd
{
	function run(array $argv);
	function returnType();
}