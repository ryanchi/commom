<?php

class CurlClass {
	
	
	public function __construct() {
		
		return true;
	}
	public function curl_json($url,$params) {
		
		$json = json_encode($params);
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'POST');
		curl_setopt($ch,CURLOPT_HEADER,0);//文件头信息是否也输出
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$json);
		$headers = array();

		$headers[] = 'Content-Type:application/json';
		//中文信息 ? mb_strlen
		$headers[] = 'Content-Length:'.strlen($json);
		//一个用来设置HTTP头字段的数组
		curl_setopt($ch,CURLOPT_HTTPHEADER,$headers); 
		$data = curl_exec($ch);
		
		if (curl_errno($ch))
		{
			$ret = array(curl_error($ch), curl_errno($ch))
			curl_close($ch);
			return $ret;
		}  else {
			
			curl_close($ch);
			
			return $data;
		}
		
		
		
	}
	
}