<?php
namespace lib;
abstract class http
{
	protected $cookie; //cookie保存路径
	public $userAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1';
	public $useCache = true;
	public function __construct($cookie_path='',$useragent='')
	{
		if ($cookie_path != '') 
		{
			$this->cookie = $cookie_path;
		}
		else
		{
			$this->cookie = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'httpget_3472e87jsk.cookie';
		}
		if ($useragent != '') 
		{
			$this->setUserAgent($useragent);
		}
		else
		{
			if (file_exists($path = __DIR__.'/../.userAgent')) 
			{
				$this->setUserAgent(file_get_contents($path));
			}
		}
	}
	public function setCacheFlag($v)
	{
		$this->useCache = !!$v;
	}
	public function getCachePath($url)
	{
		return __DIR__.'/../runtime/fetch_cache/'.md5($url);
	}
	public function cache($url,$content)
	{
		file_put_contents($this->getCachePath($url), $content);
	}
	public function setUserAgent($ua) 
	{
		$this->userAgent = $ua;
	}
	public function useFollow()
	{
		$this->userAgent = $_SERVER['HTTP_USER_AGENT'];
	}
}