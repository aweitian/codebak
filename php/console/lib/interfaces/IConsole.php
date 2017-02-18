<?php
interface IConsole
{
	function input();
	function output($data);
	function run($argv);
	function help();
	function show($info);

	//-------------------------------

	function readLine();

	//--------------------------------
	function write($data);
	function writeLn($data);
}