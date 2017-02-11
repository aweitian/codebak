<?php
class httpPost
{
	public function send($url, $data) 
	{
		if ($data) 
		{
			$data = http_build_query($data);
		}
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_POST, true); 
		curl_setopt($curl,  CURLOPT_POSTFIELDS, $data);
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