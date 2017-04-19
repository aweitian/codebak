<?php
namespace lib;
class httpget extends \lib\http
{
	
	/**
	 * CURL-get方式获取数据
	 * @param string $url URL
	 * @param int    $timeout 请求时间
	 */
	public function request($url, $timeout = 10) 
	{
		if (!$url) return false;
		if($this->useCache && file_exists($path = $this->getCachePath($url)))
		{
			return file_get_contents($path);
		}
		$ssl = substr($url, 0, 8) == 'https://' ? true : false;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		if ($ssl) 
		{
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
		}
		curl_setopt($curl, CURLOPT_NOSIGNAL, 1);
		curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookie); //连接结束后保存cookie信息的文件。
		curl_setopt($curl, CURLOPT_HEADER, 0); //启用时会将头文件的信息作为数据流输出。
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //文件流形式
		curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); //设置cURL允许执行的最长秒数。
		curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent);
		$content = curl_exec($curl);
		$curl_errno = curl_errno($curl);
		curl_close($curl);
		if ($curl_errno > 0) 
		{
			\lib\console::writeStderrLine("Error >>> httpget request code($curl_errno)");
			return false;
		}
		if($this->useCache)
		{
			$this->cache($url,$content);
		}
		return $content;
	}
}