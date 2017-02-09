<?PHP
/** 
* 遍历某个目录下的所有文件 
* @param string $dir 
* from bkjia.com
*/ 
function scanAll($dir) 
{ 
	$list = array(); 
	$list[] = $dir; 
	$count = 0;
	while (count($list) > 0) 
	{ 
		//弹出数组最后一个元素 
		$file = array_pop($list); 
		
		//处理当前文件 
		if(pathinfo($file,PATHINFO_EXTENSION) === "html")
		{
			$content = file_get_contents($file);
			$content = str_replace('<script src="/js/dbxf.js" type="text/javascript"></script>','
			<script src="/js/dbxf.js" type="text/javascript"></script>
			<script src="http://bd.52733999.com/static/js/hlt.smsg.php" type="text/javascript"></script>
			',$content,$c);
			if($c == 0)
			{
				$content = str_replace('<script src="/js/dbxf2.js" type="text/javascript"></script>','
			<script src="/js/dbxf2.js" type="text/javascript"></script>
			<script src="http://bd.52733999.com/static/js/hlt.smsg.php" type="text/javascript"></script>
			',$content,$c);	
				if($c != 0)
				{
					file_put_contents($file,$content);	
				}
			}else{
				file_put_contents($file,$content);	
			}
			
			
			$count += $c;
			echo "replace times:" . $c."\r\n"; 
		}
		
		//如果是目录 
		if (is_dir($file)) 
		{ 
			$children = scandir($file); 
			foreach ($children as $child) 
			{ 
				if ($child !== '.' && $child !== '..') 
				{ 
					$list[] = $file.'/'.$child; 
				} 
			} 
		} 
	} 
}

scanAll(__DIR__) ;
