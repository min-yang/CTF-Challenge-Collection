<?php 
namespace Helper;
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
/*
 * Name   : Collection
 * Date	  : 20120107 
 * Author : Qesy 
 * QQ	  : 762264
 * Mail   : 762264@qq.com
 *
 *(̅_̅_̅(̲̅(̅_̅_̅_̅_̅_̅_̅_̅()ڪے 
 *
*/ 

class Collection{
	private static $s_instance;
	private $_stor		= 1; //-- 0:ASC, 1:DESC 
	private $_timeOut	= 15; //-- 获取内容时间 --	
	private $_removeHtml = '<p><br><table><tr><td><img><b><h1><h2><h3><h4><h5><h6><hr>';
	public $charset		= 'UTF-8';
	public $path = '/static/images/';
	public $url;
	
	public static function get_instance(){
		if (!isset(self::$s_instance)){
			self::$s_instance = new self();
		}
		return self::$s_instance;
	}
	/*
	 * name : 获取HTML
	 * url : 'http://www.baidu.com'
	 * return ：String
	 */
	public function getStr($url){ 
		$opts = array('http'=>array('method'=>"GET",'timeout'=> $this->_timeOut));
		$context = stream_context_create($opts);
		$str = @file_get_contents($url, FALSE, $context);
		$str = self::_removeStyle(self::_removeScript($str));
		$searchArr = array("\n", "\r", "\t", "\0", "\x");
		$str = str_replace($searchArr, '', $str);
		
		if($this->charset != 'UTF-8'){
			$str = mb_convert_encoding($str, "UTF-8", "GBK"); 
		}	
		return empty($str) ? false : $str;
	}
	/*
	 * name : 获取列表（前后截取法获）
	 * return ：Array
	 */
	public function getList($start, $end, $str){ 
		if(!strpos($str, $start) || !strpos($str, $end)){
			return false;
		}			
		$startSite = strpos($str, $start)+ strlen($start);
		$str = trim(substr($str, $startSite));				
		$endSite = strpos($str, $end);
		$listStr = trim(substr($str, 0, $endSite));	
		return self::_getHref($listStr);
	}
	/*
	 * name : 获取列表（正则匹配法）
	 * return ：Array
	 */
	public function getListEreg($ereg, $str){
		preg_match($ereg, $str, $matches);
		if(empty($matches)){
			return false;
		}
		return self::_getHref($matches[1]);
	}
	/*
	 * name : 截取字符串（前后截取法）
	 * return ：String
	 * type : 0,获取内容，1：获取内容并下载内容中的图片，2:直接下载文件
	 */
	public function cutStr($start, $end, $str, $type = 0){
		if(!strpos($str, $start) || !strpos($str, $end)){
			return false;
		}
		$startSite = strpos($str, $start)+ strlen($start);
		$str = trim(substr($str, $startSite));				
		$endSite = strpos($str, $end);
		$result = trim(substr($str, 0, $endSite));	
		return $this->_actStr($result, $type);
	}
	/*
	 * name : 截取字符串（正则匹配法）
	 * return ：String
	 * type : 0,获取内容，1：获取内容并下载内容中的图片，2:直接下载文件
	 */
	public function eregStr($ereg, $str, $type = 0){ 
		preg_match($ereg, $str, $matches);	
		$result = empty($matches) ? false : trim($matches[1]);	
		return $this->_actStr($result, $type);
	}
	/*
	 * name : 操作类型
	 * return ：String
	 * type : 0,获取内容，1：获取内容并下载内容中的图片，2:直接下载文件
	 */
	private function _actStr($str, $type = 0){
		switch ($type){
			case 1:
				$result = $this->_saveImg($str);
				break;
			case 2:
				$path = $this->path.date('Ym').'/';
				$fileName = $this->_getFilename($str);
				$result = $this->_saveFile($str, $path, $fileName);
				break;
			default:
				$result = $str;
				break;
		}
		return $result;
	}
	/*
	 * name : 获取连接列表
	 * return ：Array
	 */
	private function _getHref($str){
		preg_match_all('/href="([\s\S.]*?)"/', $str, $matches);
		return empty($matches) ? false : $matches[1];
	}
	/*
	 * name : 去除HTML标签
	 * return ：String
	 */
	public function removeHtml($str){
		return strip_tags($str, $this->_removeHtml);
	}
	/*
	 * name : 去除Javascript
	 * return ：String
	 */
	private function _removeScript($str){
		preg_match_all('/<script([\s\S.]*?)<\/script>/', $str, $matches);
		if(empty($matches)){
			return $str;
		}
		return str_replace($matches[0], '', $str);
	}
	
	/*
	 * name : 去除链接
	 * return ：String
	 */
	public function removeLink($str){
	   preg_match_all('/<a ([\s\S.]*?)">([\s\S.]*?)<\/a>/', $str, $matches);
	    if(empty($matches)){
	        return $str;
	    }
	    return str_replace($matches[0], $matches[2], $str); 
	}
	/*
	 * name : 去除图片
	 * return ：String
	 */
	public function removeImg($str){
	    preg_match_all('/<img([\s\S.]*?)\/>/', $str, $matches);
	    if(empty($matches)){
	        return $str;
	    }
	    return str_replace($matches[0], '', $str);
	}
	/*
	 * name : 去除CSS
	 * return ：String
	 */
	private function _removeStyle($str){
		preg_match_all('/<style([\s\S.]*?)<\/style>/', $str, $matches);
		if(empty($matches)){
			return $str;
		}
		return str_replace($matches[0], '', $str);
	}
	/*
	 * name : 保存图片
	 * return ：String
	 */
	public function _saveImg($str){
		preg_match_all('/src=\"([\s\S.]*?)\"/', $str, $matches);
		if(empty($matches)){
			return $str;
		}
		$replaceArr = array();
		$path = $this->path.date('Ym').'/';
		foreach ($matches[1] as $v){			
			$fileName = self::_getFilename($v);
			$isRemote = substr_count($v, 'http');	
			if(empty($isRemote)){
				$url = $this->url.$v;
			}else{
				$url = $v;
			}	
			$this->_saveFile($url, $path, $fileName);
			$replaceArr[] = $path.$fileName;
		}
		return str_replace($matches[1], $replaceArr, $str);
	}
	/*
	 * name : 保存文件
	 * return ：String
	 */
	private function _saveFile($url, $webPath, $fileName){
		$sysPath = $_SERVER['DOCUMENT_ROOT'].$this->path.date('Ym').'/';
		if(!file_exists($sysPath)){
			mkdir($sysPath, 0777);
		}		
		$opts = array('http'=>array('method'=>"GET",'timeout'=> $this->_timeOut));
		$context = stream_context_create($opts);
		$get_file = @file_get_contents($url, FALSE, $context);
		$fp = fopen($sysPath.$fileName, 'w');
		if($fp){
			fwrite($fp, $get_file);
			fclose($fp);
			return $webPath.$fileName;			
		}
		return false;
	}
	/*
	 * name : 获取文件名
	 * return ：String
	 */
	private function _getFilename($url){
		$urlArr = explode('/', $url);
		return end($urlArr);
	}
}

/*
$t = new Collection();//-- 实例化类 --
$t->charset = 'gb2312';//-- 目标站编码 --
$t->url = 'http://www.shuangtv.net';//-- 下载图片路径前缀 --
$str = $t->getStr('http://www.baidu.com/');//-- 获取内容 --
$str = $t->getListEreg('/<a href=\'http:\/\/video\.sina\.cn\?pos=1&amp;vt=1\'>视频<\/a>】([\s\S.]*?)传媒<\/a><br\/>/', $str);//-- 正则获取列表 --
$str2 = $t->cutStr('<p id="lg"><img src="', '"', $str, 2);//-- 下载图片 --
$str2 = $t->eregStr('/<title>([\s\S.]*?)<\/title>/', $str);//-- 截取内容 --
var_dump($str2);*/
