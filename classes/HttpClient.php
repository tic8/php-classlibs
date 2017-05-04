<?php

namespace app\common;

use \Exception as Exception;

class HttpClient
{
    // 表单提交字符集编码
    public $postCharset = "UTF-8";
    
    
    public static function curl($url='',$postFields=null,$type='post'){
        
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$postBodyString = "";
		$encodeArray = array();
		$postMultipart = false;

		if (is_array($postFields) && 0 < count($postFields)) {

			foreach ($postFields as $k => $v) {
				if ("@" != substr($v, 0, 1)) //判断是不是文件上传
				{

					$postBodyString .= "$k=" . urlencode($v) . "&";
					$encodeArray[$k] = $v;
				} else //文件上传用multipart/form-data，否则用www-form-urlencoded
				{
					$postMultipart = true;
					$encodeArray[$k] = new \CURLFile(substr($v, 1));
				}

			}
			unset ($k, $v);
			curl_setopt($ch, CURLOPT_POST, true);
			if ($postMultipart) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $encodeArray);
			} else {
				curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
			}
		}

		if ($postMultipart) {
		    list($s1, $s2) = explode(' ', microtime());
		    $boundary = (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
			$headers = array('content-type: multipart/form-data;charset=utf-8;boundary='.$boundary);
		} else {

			$headers = array('content-type: application/x-www-form-urlencoded;charset=utf-8');
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$reponse = curl_exec($ch);

		if (curl_errno($ch)) {
			throw new Exception(curl_error($ch), 0);
		} else {
			$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if (200 !== $httpStatusCode) {
				throw new Exception($reponse, $httpStatusCode);
			}
		}

		curl_close($ch);
		return $reponse;
        
    }
    
    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @return 提交表单HTML文本
     */
    public static function buildRequestForm($data=[],$url='') {
        $sHtml = "<form id='submit' name='submit' action='".$url."' method='POST'>";
        foreach($data as $key => $val){
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
        $sHtml = $sHtml."<script>document.forms['submit'].submit();</script>";
        return $sHtml;
    }
    
}