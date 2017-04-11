<?PHP
$cmdList = [
	1,
	[2,3,4,5],
	[6,10],
	7

];

function run($stdin,$cur=0)
{
	global $cmdList;
	if($cur >= count($cmdList))
		return;
	$cmd=$cmdList[$cur];
	if(is_array($cmd))
	{
		foreach($cmd as $c)
		{
			
			runSingleTask($stdin,$c,$cur+1);
		}	
	}
	else
	{
		runSingleTask($stdin,$cmd,$cur+1);
	}
	
}
function runSingleTask($stdin,$data,$cur)
{
	echo "stdin:$stdin,now:$data\n";
	run($data,$cur);	
}
run("init");