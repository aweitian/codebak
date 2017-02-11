<?php
class httpGet
{
	public function send($url) 
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($curl, CURLOPT_TIMEOUT, 10); 
		$content = curl_exec($curl);
		$curl_errno = curl_errno($curl);
		curl_close($curl);
		if ($curl_errno > 0) 
		{
			throw new Exception($curl_errno);
			return false;
		}
		return $content;
	}
}