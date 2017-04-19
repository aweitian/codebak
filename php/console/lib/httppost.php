<?php
namespace lib;
class httppost extends http
{
	/**
	 * CURL-post方式获取数据
	 * @param string $url URL
	 * @param array  $data POST数据
	 * @param int    $timeout 请求时间
	 */
	public function request($url, $data, $timeout = 10) 
	{
		if (!$url) return false;
		if($this->useCache && file_exists($path = $this->getCachePath($url)))
		{
			return file_get_contents($path);
		}
		if ($data) 
		{
			//$data = http_build_query($data);
		}
		$ssl = substr($url, 0, 8) == 'https://' ? true : false;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		if ($ssl) 
		{
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
		}
		curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookie); //连接结束后保存cookie信息的文件。
		curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookie);//包含cookie数据的文件名，cookie文件的格式可以是Netscape格式，或者只是纯HTTP头部信息存入文件。
		curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent); //在HTTP请求中包含一个"User-Agent: "头的字符串。
		curl_setopt($curl, CURLOPT_HEADER, 0); //启用时会将头文件的信息作为数据流输出。
		curl_setopt($curl, CURLOPT_POST, true); //发送一个常规的Post请求
		curl_setopt($curl,  CURLOPT_POSTFIELDS, $data);//Post提交的数据包
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); //启用时会将服务器服务器返回的"Location: "放在header中递归的返回给服务器，使用CURLOPT_MAXREDIRS可以限定递归返回的数量。
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //文件流形式
		curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); //设置cURL允许执行的最长秒数。
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