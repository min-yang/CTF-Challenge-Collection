<?php
use Model\QC_Sys;

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
class Router {
	private $_SiteConfig;
	private static $s_instance;
	public static $s_Controller;
	public static $s_Method;
	function __construct() {
		$this->_SiteConfig = SiteConfig ();
		self::ViewController ();
	}
	public static function get_instance() {
		if (! isset ( self::$s_instance )) self::$s_instance = new self ();
		return self::$s_instance;
	}
	private function _FetchUrl($Url) {
		$RouterArr = array ();	
	   if (php_sapi_name () == 'cli') {
			$Url = $_SERVER ['argv'][1]; //路径解析 例 首页 index/index
			$ParaArr = array_slice ($_SERVER ['argv'] , 2 );
			if(!empty($ParaArr)){
			    foreach($ParaArr as $v){
			        $Sub = explode('=', $v);
			        $_GET[$Sub[0]] = $Sub[1];
			    }
			}
		}
		if (strpos ( $Url, 'poweredByQesy' ) !== false) {
			echo "powered By Qesy <br>\n";
			echo "Email : 762264@qq.com <br>\n";
			echo "Version : QFrame v 1.0.0 <br>\n";
			echo "Your Ip : " . $_SERVER['REMOTE_ADDR'] . "<br>\n";
			echo "Date : " . date ( 'Y-m-d H:i:s' ) . "<br>\n";
			echo "UserAgent : " . $_SERVER ['HTTP_USER_AGENT'] . "<br>\n";
			exit ();
		}
		if ($Url == false) {			
			$RouterArr ['Name'] = $this->_SiteConfig ['DefaultController'];
			$RouterArr ['Url'] = PATH_SYS . 'Controller/' . $this->_SiteConfig ['DefaultController'] . EXTEND;
			$RouterArr ['Method'] = $this->_SiteConfig ['DefaultFunction'];
		} else {
			$UrlArr = explode ( $this->_SiteConfig ['Url'], $Url );
			$UrlTmp = '';
			foreach ( $UrlArr as $key => $val ) {
				$File = $UrlTmp . $val;
				$UrlTmp .= $val . $this->_SiteConfig ['Url'];
				if (file_exists ( PATH_SYS . 'Controller/' . $File . EXTEND )) {
					$RouterArr ['Name'] = $val;
					$RouterArr ['Url'] = PATH_SYS . 'Controller/' . $File . EXTEND;
					$FunUrl = substr ( $Url, strlen ( $File ) + 1 );
					$FunArr = explode ( $this->_SiteConfig ['Url'], $FunUrl );
					$RouterArr ['Method'] = empty ( $FunArr [0] ) ? 'index' : $FunArr [0];
					$RouterArr ['ParaArr'] = array_splice ( $FunArr, 1 );
					break;
				}
			}
		}
		return empty ( $RouterArr ) ? array (
				'Name' => 'home',
				'Url' => PATH_SYS . 'Controller/home.php',
				'Method' => 'err',
				'ParaArr' => array () 
		) : $RouterArr;
	}
	private function ViewController() {
		$Url = URL_CURRENT;
		$HtnlExtend = stripos($Url, $this->_SiteConfig['Extend']);
		$XmlExtend = stripos($Url, '.xml');
		if($HtnlExtend !== false) $Url = substr($Url, 0, $HtnlExtend);
		if($XmlExtend !== false) $Url = substr($Url, 0, $XmlExtend);
		if($this->_SiteConfig['UrlType'] == 1) $Url = self::_UrlConvent($Url);
		$RouterArr = self::_FetchUrl ($Url);
		require $RouterArr ['Url'];
		if (! method_exists ( $RouterArr ['Name'], $RouterArr ['Method'] . '_Action' )) {
			$RouterArr = array (
					'Name' => 'home',
					'Url' => PATH_SYS . 'Controller/home.php',
					'Method' => 'err',
					'ParaArr' => array () 
			);
			require_once $RouterArr ['Url'];
		}
		self::$s_Controller = $RouterArr ['Name'];
		self::$s_Method = $RouterArr ['Method'];
		Base::InsertFuncArray ( $RouterArr );
	}
	
	private function _UrlConvent($Url){
	    if(strpos($Url, 'install') === 0) return $Url; //安装文件不处理
	    $SysObj = QC_Sys::get_instance();
	    if(strpos($Url, 'cate/') !== false){
	        $ListUrl = 'cate/'.$SysObj->getOne('UrlList')['AttrValue'];
	        $ListUrlRep = '/^'.str_replace(array('/', '{CateId}', '{PinYin}', '{PY}'), array('\/', '(\d+)', '([\w]*?)', '([\w]*?)'), $ListUrl).'$/';
            if(preg_match($ListUrlRep, $Url.$this->_SiteConfig['Extend'], $Matches)){ //匹配列表	            
	            preg_match_all("/\{([a-zA-Z0-9]+)\}/",$ListUrl, $MatchesSort);
	            $CateId = 0;
	            foreach($MatchesSort[1] as $k => $v){
	                $$v = $Matches[$k+1];
	            }
	            return 'index/cate/'.$CateId;
	        }
	    }
	    
	    if(strpos($Url, 'detail/') !== false){	       
	        $UrlDetail = 'detail/'.$SysObj->getOne('UrlDetail')['AttrValue'];
	        $UrlDetailRep = '/^'.str_replace(array('/', '{Y}', '{M}', '{D}', '{Id}', '{PinYin}', '{PY}'), array('\/', '(\d+)', '(\d+)', '(\d+)', '(\d+)','([\w]*?)', '([\w]*?)'), $UrlDetail).'$/';
	        if(preg_match($UrlDetailRep, $Url.$this->_SiteConfig['Extend'], $Matches)){ //匹配详情	            
	            preg_match_all("/\{([a-zA-Z0-9]+)\}/",$UrlDetail, $MatchesSort);
	            $Id = 0;
	            foreach($MatchesSort[1] as $k => $v){
	                $$v = $Matches[$k+1];
	            }
	            return 'index/detail/'.$Id;
	        }
	    }
	    
	    if(strpos($Url, 'page/') !== false){
	        $UrlPage = 'page/'.$SysObj->getOne('UrlPage')['AttrValue'];
	        $UrlPageRep = '/^'.str_replace(array('/', '{PageId}', '{PinYin}', '{PY}'), array('\/', '(\d+)','([\w]*?)', '([\w]*?)'), $UrlPage).'$/';
	        if(preg_match($UrlPageRep, $Url.$this->_SiteConfig['Extend'], $Matches)){ //匹配详情
	            preg_match_all("/\{([a-zA-Z0-9]+)\}/",$UrlPage, $MatchesSort);
	            $PageId = 0;
	            foreach($MatchesSort[1] as $k => $v){
	                $$v = $Matches[$k+1];
	            }
	            return 'index/page/'.$PageId;
	        }
	    }
	    
	    if(strpos($Url, 'form/') !== false){
	        $UrlPage = 'form/'.$SysObj->getOne('UrlForm')['AttrValue'];	        
	        $UrlPageRep = '/^'.str_replace(array('/', '{KeyName}'), array('\/', '([\w\W]*?)'), $UrlPage).'$/';
	        if(preg_match($UrlPageRep, $Url.$this->_SiteConfig['Extend'], $Matches)){ //匹配详情
	            preg_match_all("/\{([a-zA-Z0-9]+)\}/",$UrlPage, $MatchesSort);
	            $KeyName = 0;
	            foreach($MatchesSort[1] as $k => $v){
	                $$v = $Matches[$k+1];
	            }
	            return 'index/form/'.$KeyName;
	        }
	    }
	    
	    return $Url;
	}
}
?>