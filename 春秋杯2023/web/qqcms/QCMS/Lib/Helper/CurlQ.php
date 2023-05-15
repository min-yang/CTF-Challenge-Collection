<?php
namespace Helper; 
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
/*
 * Name : Collection
 * Date : 20120107
 * Author : Qesy
 * QQ : 762264
 * Mail : 762264@qq.com
 *
 * (̅_̅_̅(̲̅(̅_̅_̅_̅_̅_̅_̅_̅()ڪے
 *
 */
class CurlQ {
	private static $s_instance;
	public $ParaArr = array(
	    'Url' => '',
	    'IsPost' => false,
	    'IsHttps' => false,
	    'Para' => array(),
	    'Header' => array(),
	    'IsJson' => true,
	    'IsDebug' => false,
	);
	const CURLOPT_SSLCERT_PATH = '';
	const CURLOPT_SSLKEY_PATH = '';
	
	public static function get_instance() {
		if (! isset ( self::$s_instance )) {
			self::$s_instance = new self ();
		}
		return self::$s_instance;
	}
	
	public function SetIsJson($IsJson){
	    $this->ParaArr['IsJson'] = $IsJson;
	    return $this;
	}
	
	public function SetDebug($IsDebug = 0){
	    $this->ParaArr['IsDebug'] = $IsDebug;
	    return $this;
	}
	
	public function SetUrl($Url){
	    $this->ParaArr['Url'] = $Url;
	    return $this;
	}
	
	public function SetIsPost($IsPost){
	    $this->ParaArr['IsPost'] = $IsPost;
	    return $this;
	}
	
	public function SetIsHttps($IsHttps){
	    $this->ParaArr['IsHttps'] = $IsHttps;
	    return $this;
	}
	
	public function SetPara($Para){
	    $this->ParaArr['Para'] = $Para;
	    return$this;
	}
	
	public function SetHeader($Header){
	    $this->ParaArr['Header'] = $Header;
	    return $this;
	}
	
	public function Execute(){
	    $Url = $this->ParaArr['Url'];
	    if($this->ParaArr['IsPost'] == false && !empty($this->ParaArr['Para'])){
	        $Url = $this->ParaArr['Url'].'?'.http_build_query($this->ParaArr['Para']);
	    }	    
	    $Ch = curl_init($Url);	    
	    if($this->ParaArr['IsPost']) {
	        curl_setopt($Ch, CURLOPT_POST, true );
	        $Para = ($this->ParaArr['IsJson']) ? json_encode($this->ParaArr['Para'], JSON_UNESCAPED_UNICODE) : $this->ParaArr['Para'];	       
	        curl_setopt($Ch, CURLOPT_POSTFIELDS, $Para);
	    }
	    if(!empty($this->ParaArr['Header'])) curl_setopt($Ch, CURLOPT_HTTPHEADER, $this->ParaArr['Header']); 
	    if($this->ParaArr['IsHttps']){
	        curl_setopt($Ch, CURLOPT_SSL_VERIFYPEER, 0);
	        curl_setopt($Ch, CURLOPT_SSL_VERIFYHOST, 1);
	       /*  curl_setopt($Ch, CURLOPT_SSLCERTTYPE, 'PEM');
	        curl_setopt($Ch, CURLOPT_SSLCERT, self::CURLOPT_SSLCERT_PATH);
	        curl_setopt($Ch, CURLOPT_SSLKEYTYPE, 'PEM');
	        curl_setopt($Ch, CURLOPT_SSLKEY, self::CURLOPT_SSLKEY_PATH); */
	    }
	    curl_setopt ($Ch, CURLOPT_RETURNTRANSFER, true );
	    $Result = curl_exec ($Ch);
	    $Info = curl_getinfo ($Ch);
	    if($this->ParaArr['IsDebug']) {
	        echo '<br>ParaArr : ';var_dump($this->ParaArr);
	        echo '<br>Info : ';var_dump($Info);
	        echo '<br>Result : ';echo ($Result);
	    }
	    curl_close ($Ch);
	    self::_Clean();
	    return $Result;
	}
	
	private function _Clean(){
	    $this->ParaArr = array(
	        'Url' => '',
    	    'IsPost' => false,
    	    'IsHttps' => false,
    	    'Para' => array(),
    	    'Header' => array(),
    	    'IsJson' => true,
    	    'IsDebug' => false,
	    );
	}
}